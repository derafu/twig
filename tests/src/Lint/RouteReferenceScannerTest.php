<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Lint;

use Derafu\Routing\Router;
use Derafu\Twig\Cache\CacheItemPool;
use Derafu\Twig\Extension\RoutingExtension;
use Derafu\Twig\Extension\TranslationExtension;
use Derafu\Twig\Lint\RouteReference;
use Derafu\Twig\Lint\RouteReferenceScanner;
use Derafu\Twig\Lint\TemplateFinder;
use Derafu\Twig\NodeVisitor\TranslationDefaultDomainNodeVisitor;
use Derafu\Twig\Provider\AllComponentProvider;
use Derafu\Twig\Provider\DirectoryComponentProvider;
use Derafu\Twig\Service\ComponentRegistrar;
use Derafu\Twig\Service\TwigCreator;
use Derafu\Twig\Service\TwigService;
use Derafu\Twig\TokenParser\TransDefaultDomainTokenParser;
use Derafu\Twig\TokenParser\TransTokenParser;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

/**
 * Finds the names of the routes that the templates refer to (`path()`, `url()`
 * and `is_active_path()`) by reading what Twig itself parses, so comments,
 * texts and strings that only look like a call are never mistaken for one.
 *
 * It only finds references: whether a route exists is for whoever uses it to
 * decide, with the router of the templates it scanned.
 */
#[CoversClass(RouteReferenceScanner::class)]
#[CoversClass(RouteReference::class)]
#[UsesClass(RoutingExtension::class)]
#[UsesClass(TemplateFinder::class)]
#[UsesClass(TranslationExtension::class)]
#[UsesClass(TranslationDefaultDomainNodeVisitor::class)]
#[UsesClass(TransDefaultDomainTokenParser::class)]
#[UsesClass(TransTokenParser::class)]
#[UsesClass(TwigService::class)]
#[UsesClass(TwigCreator::class)]
#[UsesClass(ComponentRegistrar::class)]
#[UsesClass(AllComponentProvider::class)]
#[UsesClass(DirectoryComponentProvider::class)]
#[UsesClass(CacheItemPool::class)]
final class RouteReferenceScannerTest extends TestCase
{
    /**
     * @var list<string>
     */
    private array $directories = [];

    protected function tearDown(): void
    {
        foreach ($this->directories as $directory) {
            $this->remove($directory);
        }
    }

    private function remove(string $path): void
    {
        if (is_file($path)) {
            unlink($path);

            return;
        }

        foreach (scandir($path) ?: [] as $entry) {
            if ($entry !== '.' && $entry !== '..') {
                $this->remove($path . '/' . $entry);
            }
        }

        rmdir($path);
    }

    /**
     * @param array<string, string> $templates
     */
    private function environment(array $templates): Environment
    {
        $twig = new Environment(new ArrayLoader($templates));
        $twig->addExtension(new RoutingExtension(new Router()));

        return $twig;
    }

    /**
     * @return list<RouteReference>
     */
    private function scan(string $source): array
    {
        return (new RouteReferenceScanner($this->environment(['t.twig' => $source])))
            ->scanTemplate('t.twig')
        ;
    }

    /**
     * @param list<RouteReference> $references
     * @return list<string|null>
     */
    private function names(array $references): array
    {
        return array_map(fn (RouteReference $reference) => $reference->name, $references);
    }

    public function testItFindsTheNameOfEachRoutingFunction(): void
    {
        $references = $this->scan(
            "{{ path('docs') }}{{ url('blog') }}{% if is_active_path('faq') %}x{% endif %}"
        );

        $this->assertSame(
            [['path', 'docs'], ['url', 'blog'], ['is_active_path', 'faq']],
            array_map(fn (RouteReference $r) => [$r->function, $r->name], $references)
        );
        $this->assertSame(['t.twig', 't.twig', 't.twig'], array_map(fn (RouteReference $r) => $r->template, $references));
    }

    public function testItReportsTheLineOfEachReference(): void
    {
        $references = $this->scan("text\n\n{{ path('a') }}\n<p>{{ url('b') }}</p>\n");

        $this->assertSame([3, 4], array_map(fn (RouteReference $r) => $r->line, $references));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function dynamicNamesProvider(): array
    {
        return [
            'variable' => ["{{ path(route) }}"],
            'attribute' => ["{{ path(content.route.name) }}"],
            'method call' => ["{{ url(item.route().name) }}"],
            'concatenation' => ["{{ path('docs_' ~ kind) }}"],
            'condition' => ["{{ path(flag ? 'a' : 'b') }}"],
            'filter' => ["{{ path(name|lower) }}"],
        ];
    }

    #[DataProvider('dynamicNamesProvider')]
    public function testANameThatIsNotALiteralIsReportedAsDynamic(string $source): void
    {
        $references = $this->scan($source);

        $this->assertCount(1, $references);
        $this->assertNull($references[0]->name);
        $this->assertTrue($references[0]->isDynamic());
    }

    public function testALiteralIsNotDynamic(): void
    {
        $this->assertFalse($this->scan("{{ path('docs') }}")[0]->isDynamic());
    }

    public function testItIgnoresWhatOnlyLooksLikeACall(): void
    {
        $references = $this->scan(
            "{# path('in_a_comment') #}path('in_the_text'){{ \"path('in_a_string')\" }}"
            . "{% verbatim %}{{ path('in_verbatim') }}{% endverbatim %}"
        );

        $this->assertSame([], $references);
    }

    public function testItFindsTheReferencesWhereverTwigAllowsACall(): void
    {
        $references = $this->scan(<<<'TWIG'
            {% macro m() %}{{ path('in_macro') }}{% endmacro %}
            {% set a = url('in_set') %}
            {% for i in [1] %}{{ path('in_for') }}{% endfor %}
            {{ 'x'|replace({'x': path('in_filter')}) }}
            {{ path('outer', {'p': url('inner')}) }}
            TWIG);

        $names = $this->names($references);
        sort($names);

        $this->assertSame(
            ['in_filter', 'in_for', 'in_macro', 'in_set', 'inner', 'outer'],
            $names
        );
    }

    public function testItAcceptsTheNameAsANamedArgument(): void
    {
        $this->assertSame(['docs'], $this->names($this->scan("{{ path(name: 'docs') }}")));
    }

    public function testItScansTheBlocksOfAChildTemplate(): void
    {
        $scanner = new RouteReferenceScanner($this->environment([
            'base.twig' => '{% block b %}{% endblock %}',
            'child.twig' => "{% extends 'base.twig' %}{% block b %}{{ path('in_block') }}{% endblock %}",
        ]));

        $this->assertSame(['in_block'], $this->names($scanner->scanTemplate('child.twig')));
    }

    public function testItOnlyLooksForTheFunctionsOfTheRoutingExtension(): void
    {
        $twig = $this->environment(['t.twig' => "{{ asset('logo') }}{{ path('docs') }}"]);
        $twig->addExtension(new class () extends AbstractExtension {
            public function getFunctions(): array
            {
                return [new TwigFunction('asset', fn (string $name): string => $name)];
            }
        });

        $this->assertSame(['docs'], $this->names((new RouteReferenceScanner($twig))->scanTemplate('t.twig')));
    }

    /**
     * A function is recognized by what it is (it belongs to the routing
     * extension), not by its name: another `path()` is not a route.
     */
    public function testAFunctionNamedPathFromAnotherExtensionIsNotARoutingFunction(): void
    {
        $twig = new Environment(new ArrayLoader(['t.twig' => "{{ path('x') }}"]));
        $twig->addExtension(new class () extends AbstractExtension {
            public function getFunctions(): array
            {
                return [new TwigFunction('path', fn (string $name): string => $name)];
            }
        });

        $this->expectException(LogicException::class);

        (new RouteReferenceScanner($twig))->scanTemplate('t.twig');
    }

    /**
     * Finding nothing because the environment has no routing functions would
     * look the same as a clean template: it must fail instead.
     */
    public function testItFailsWhenTheEnvironmentHasNoRoutingFunctions(): void
    {
        $twig = new Environment(new ArrayLoader(['t.twig' => "{{ path('x') }}"]));

        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('/RoutingExtension/');

        (new RouteReferenceScanner($twig))->scanTemplate('t.twig');
    }

    /**
     * The scanner takes the name from the first argument of every function of
     * the routing extension: if one is added that does not work like that, this
     * must fail so the scanner is changed with it.
     */
    public function testEveryFunctionOfTheRoutingExtensionTakesTheRouteNameFirst(): void
    {
        $extension = new RoutingExtension(new Router());

        $this->assertNotEmpty($extension->getFunctions());

        foreach ($extension->getFunctions() as $function) {
            $callable = $function->getCallable();
            $this->assertIsArray($callable);

            $first = (new ReflectionMethod($callable[0], $callable[1]))->getParameters()[0] ?? null;

            $this->assertNotNull($first, sprintf('"%s" has no parameters.', $function->getName()));
            $this->assertSame('name', $first->getName(), sprintf('"%s" does not take the name first.', $function->getName()));
        }
    }

    public function testASyntaxErrorIsNotHidden(): void
    {
        $scanner = new RouteReferenceScanner($this->environment(['bad.twig' => "{{ not_a_function('x') }}"]));

        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessageMatches('/bad\.twig/');

        $scanner->scanTemplate('bad.twig');
    }

    private function directoryWith(array $files): string
    {
        $directory = sys_get_temp_dir() . '/twig-lint-' . uniqid();
        mkdir($directory . '/sub', 0777, true);
        $this->directories[] = $directory;

        foreach ($files as $file => $content) {
            file_put_contents($directory . '/' . $file, $content);
        }

        return $directory;
    }

    public function testItScansEveryTemplateOfADirectory(): void
    {
        $directory = $this->directoryWith([
            'b.html.twig' => "{{ url('in_b') }}",
            'a.html.twig' => "{{ path('in_a') }}",
            'sub/c.html.twig' => "\n{{ path('in_c') }}",
            'notes.txt' => "{{ path('not_a_template') }}",
        ]);

        $twig = new Environment(new FilesystemLoader($directory));
        $twig->addExtension(new RoutingExtension(new Router()));

        $references = (new RouteReferenceScanner($twig))->scanDirectory($directory);

        // In order of template, then line, whatever the order of the disk.
        $this->assertSame(
            [['a.html.twig', 'in_a'], ['b.html.twig', 'in_b'], ['sub/c.html.twig', 'in_c']],
            array_map(fn (RouteReference $r) => [$r->template, $r->name], $references)
        );
        $this->assertSame(2, $references[2]->line);
    }

    public function testItFailsWhenTheDirectoryDoesNotExist(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new RouteReferenceScanner($this->environment([])))->scanDirectory('/does/not/exist');
    }

    /**
     * It works with the real environment of the service, the one that has the
     * components (`<twig:...>`), which is the one the templates are written for.
     */
    public function testItParsesTheTemplatesThatUseComponents(): void
    {
        $directory = $this->directoryWith([
            'page.html.twig' => '<twig:block-alert content="Hello" type="info" /><a href="{{ path(\'docs\') }}">x</a>',
        ]);

        $service = new TwigService([
            'extra' => false,
            'paths' => [$directory, realpath(__DIR__ . '/../../../resources/templates')],
            'extensions' => [new RoutingExtension(new Router())],
        ]);

        $references = (new RouteReferenceScanner($service->getTwig()))->scanTemplate('page.html.twig');

        $this->assertSame(['docs'], $this->names($references));
    }
}
