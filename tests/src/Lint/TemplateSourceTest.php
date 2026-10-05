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

use Derafu\Twig\Cache\CacheItemPool;
use Derafu\Twig\Extension\TranslationExtension;
use Derafu\Twig\Lint\TemplateSource;
use Derafu\Twig\Node\TransDefaultDomainNode;
use Derafu\Twig\Node\TransNode;
use Derafu\Twig\NodeVisitor\TranslationDefaultDomainNodeVisitor;
use Derafu\Twig\Provider\AllComponentProvider;
use Derafu\Twig\Provider\DirectoryComponentProvider;
use Derafu\Twig\Service\ComponentRegistrar;
use Derafu\Twig\Service\TwigCreator;
use Derafu\Twig\Service\TwigService;
use Derafu\Twig\TokenParser\TransDefaultDomainTokenParser;
use Derafu\Twig\TokenParser\TransTokenParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;

/**
 * A template is read with the lines of its file, also when the environment
 * writes its code again before Twig parses it: the `<twig:...>` tags of the
 * components of several lines become one line.
 */
#[CoversClass(TemplateSource::class)]
#[UsesClass(TwigCreator::class)]
#[UsesClass(TwigService::class)]
#[UsesClass(ComponentRegistrar::class)]
#[UsesClass(AllComponentProvider::class)]
#[UsesClass(DirectoryComponentProvider::class)]
#[UsesClass(CacheItemPool::class)]
#[UsesClass(TranslationExtension::class)]
#[UsesClass(TranslationDefaultDomainNodeVisitor::class)]
#[UsesClass(TransDefaultDomainTokenParser::class)]
#[UsesClass(TransTokenParser::class)]
#[UsesClass(TransDefaultDomainNode::class)]
#[UsesClass(TransNode::class)]
final class TemplateSourceTest extends TestCase
{
    /**
     * @var list<string>
     */
    private array $directories = [];

    protected function tearDown(): void
    {
        foreach ($this->directories as $directory) {
            foreach (scandir($directory) ?: [] as $entry) {
                if ($entry !== '.' && $entry !== '..') {
                    unlink($directory . '/' . $entry);
                }
            }
            rmdir($directory);
        }
    }

    /**
     * An environment with components, so the code is written again.
     */
    private function environment(string $source): Environment
    {
        $directory = sys_get_temp_dir() . '/derafu-twig-source-' . bin2hex(random_bytes(4));
        mkdir($directory);
        $this->directories[] = $directory;
        file_put_contents($directory . '/t.html.twig', $source);

        return (new TwigService([
            'extra' => false,
            'paths' => [$directory, (string) realpath(__DIR__ . '/../../../resources/templates')],
        ]))->getTwig();
    }

    private function plain(string $source): Environment
    {
        $twig = new Environment(new ArrayLoader(['t.html.twig' => $source]));
        $twig->addExtension(new TranslationExtension());

        return $twig;
    }

    public function testWithoutComponentsTheLinesAreTheSame(): void
    {
        $template = TemplateSource::load($this->plain("a\n\n{{ b }}\nc"), 't.html.twig');

        $this->assertSame([1, 2, 3, 4], array_map($template->line(...), [1, 2, 3, 4]));
        $this->assertSame('{{ b }}', $template->text(3));
        $this->assertSame('t.html.twig', $template->name);
        $this->assertSame('t.html.twig', $template->file);
    }

    public function testATagOfSeveralLinesIsTheLineWhereItStarts(): void
    {
        $template = TemplateSource::load($this->environment(
            "<p>one</p>\n"                       // 1
            . "<twig:block-alert\n"              // 2
            . "    content=\"A\"\n"              // 3
            . "    type=\"info\"\n"              // 4
            . "/>\n"                             // 5
            . "<p>two</p>\n"                     // 6
            . "{{ three }}\n"                    // 7
            . "<twig:block-alert\n"              // 8
            . "    content=\"B\"\n"              // 9
            . "/>\n"                             // 10
            . "{{ four }}"                       // 11
        ), 't.html.twig');

        // Twig counts `<p>two</p>` as line 3, `{{ three }}` as 4 and `{{ four }}` as
        // 6, because each tag is one line.
        $this->assertSame(
            [1 => 1, 2 => 2, 3 => 6, 4 => 7, 5 => 8, 6 => 11],
            array_combine([1, 2, 3, 4, 5, 6], array_map($template->line(...), [1, 2, 3, 4, 5, 6]))
        );
    }

    public function testTheTextOfALineIsTheOneThatTwigParsed(): void
    {
        $template = TemplateSource::load($this->environment(
            "<twig:block-alert\n    content=\"A\"\n/>\n{{ 'Close'|trans }}"
        ), 't.html.twig');

        $this->assertStringContainsString("{{ 'Close'|trans }}", $template->text(2));
    }

    public function testItGivesTheFileOfTheTemplate(): void
    {
        $directory = (string) realpath(__DIR__ . '/../../../resources/templates');
        $twig = (new TwigCreator())->create(['extra' => false, 'paths' => [$directory]]);

        $template = TemplateSource::load($twig, 'components/block-alert.html.twig');

        $this->assertSame($directory . '/components/block-alert.html.twig', $template->file);
    }

    public function testATemplateThatCanNotBeParsedIsNotHidden(): void
    {
        $this->expectException(SyntaxError::class);

        TemplateSource::load($this->plain('{% trans %}{{ not_plain }}{% endtrans %}'), 't.html.twig');
    }
}
