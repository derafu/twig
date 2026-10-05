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

use Derafu\Translation\Lint\MessageReference;
use Derafu\Twig\Cache\CacheItemPool;
use Derafu\Twig\Extension\TranslationExtension;
use Derafu\Twig\Lint\TemplateFinder;
use Derafu\Twig\Lint\TemplateMessageScanner;
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
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;

/**
 * Finds the messages that the templates translate (the `trans` filter, the `t()`
 * function and the `{% trans %}` tag) by reading what Twig itself parses, so
 * comments, texts and strings that only look like a call are never mistaken for
 * one.
 *
 * It only finds messages: whether each one has its translation is for whoever
 * uses it to decide, with the catalogues of the templates it scanned.
 */
#[CoversClass(TemplateMessageScanner::class)]
#[UsesClass(TemplateFinder::class)]
#[UsesClass(TwigService::class)]
#[UsesClass(TwigCreator::class)]
#[UsesClass(ComponentRegistrar::class)]
#[UsesClass(AllComponentProvider::class)]
#[UsesClass(DirectoryComponentProvider::class)]
#[UsesClass(CacheItemPool::class)]
#[UsesClass(TemplateSource::class)]
#[UsesClass(TranslationExtension::class)]
#[UsesClass(TransNode::class)]
#[UsesClass(TransDefaultDomainNode::class)]
#[UsesClass(TranslationDefaultDomainNodeVisitor::class)]
#[UsesClass(TransTokenParser::class)]
#[UsesClass(TransDefaultDomainTokenParser::class)]
final class TemplateMessageScannerTest extends TestCase
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
     * An environment with components (the code of the templates is written
     * again before Twig parses it), over templates in a directory.
     *
     * @param array<string, string> $templates
     */
    private function componentsEnvironment(array $templates): Environment
    {
        $directory = sys_get_temp_dir() . '/derafu-twig-components-' . bin2hex(random_bytes(4));
        mkdir($directory);
        $this->directories[] = $directory;
        foreach ($templates as $name => $source) {
            file_put_contents($directory . '/' . $name, $source);
        }

        return (new TwigService([
            'extra' => false,
            'paths' => [$directory, (string) realpath(__DIR__ . '/../../../resources/templates')],
        ]))->getTwig();
    }

    private function environment(string $source): Environment
    {
        $twig = new Environment(new ArrayLoader(['t.twig' => $source]));
        $twig->addExtension(new TranslationExtension());

        return $twig;
    }

    /**
     * @return list<MessageReference>
     */
    private function scan(string $source, ?string $defaultDomain = null): array
    {
        return (new TemplateMessageScanner($this->environment($source), $defaultDomain))->scanTemplate('t.twig');
    }

    /**
     * @param list<MessageReference> $references
     * @return list<array{string|null, string|null}>
     */
    private function messages(array $references): array
    {
        return array_map(fn (MessageReference $r) => [$r->id, $r->domain], $references);
    }

    public function testItFindsTheMessageOfEachWayToTranslate(): void
    {
        $references = $this->scan(
            "{{ 'Filter'|trans({}, 'a') }}{{ t('Function', {}, 'b') }}{% trans %}Tag{% endtrans %}"
        );

        $this->assertSame(
            [['Filter', 'a'], ['Function', 'b'], ['Tag', 'messages']],
            $this->messages($references)
        );
    }

    public function testItFindsTheMessageWithTheParametersAndTheLocale(): void
    {
        $references = $this->scan(
            "{{ 'Hello {name}'|trans({'name': user}, 'a', 'es') }}"
            . "{% trans with {'n': 1} %}Count {n}{% endtrans %}"
        );

        $this->assertSame([['Hello {name}', 'a'], ['Count {n}', 'messages']], $this->messages($references));
    }

    public function testItAcceptsTheArgumentsByTheirName(): void
    {
        $references = $this->scan("{{ 'Filter'|trans(domain: 'a') }}{{ t(message: 'Function', domain: 'b') }}");

        $this->assertSame([['Filter', 'a'], ['Function', 'b']], $this->messages($references));
    }

    public function testTheMessagesAreInOrderOfLineAndTheyHaveTheirLine(): void
    {
        $references = $this->scan("text\n\n{{ 'A'|trans }}\n<p>{{ 'B'|trans }}</p>\n{% trans %}C{% endtrans %}");

        $this->assertSame(['A', 'B', 'C'], array_map(fn (MessageReference $r) => $r->id, $references));
        $this->assertSame([3, 4, 5], array_map(fn (MessageReference $r) => $r->line, $references));
    }

    public function testTheDomainOfTheTemplateIsTheOneOfTheMessagesThatDoNotGiveOne(): void
    {
        $references = $this->scan(
            "{% trans_default_domain 'twig+intl-icu' %}"
            . "{{ 'One'|trans }}{{ 'Two'|trans({}, 'own') }}{% trans %}Three{% endtrans %}"
        );

        $this->assertSame(
            [['One', 'twig+intl-icu'], ['Two', 'own'], ['Three', 'twig+intl-icu']],
            $this->messages($references)
        );
    }

    public function testTheFunctionDoesNotUseTheDomainOfTheTemplate(): void
    {
        $references = $this->scan("{% trans_default_domain 'twig+intl-icu' %}{{ t('One') }}");

        $this->assertSame([['One', 'messages']], $this->messages($references));
    }

    public function testTheMessagesThatDoNotHaveADomainUseTheOneOfTheExtension(): void
    {
        $references = $this->scan("{{ 'A'|trans }}{{ t('B') }}{% trans %}C{% endtrans %}{{ 'D'|trans({}, null) }}", 'app');

        $this->assertSame([['A', 'app'], ['B', 'app'], ['C', 'app'], ['D', 'app']], $this->messages($references));
    }

    public function testADomainThatIsNotALiteralIsReportedAsDynamic(): void
    {
        $references = $this->scan(
            "{{ 'A'|trans({}, kind) }}\n{% trans_default_domain kind %}{{ 'B'|trans }}{% trans %}C{% endtrans %}"
        );

        $this->assertSame([['A', null], ['B', null], ['C', null]], $this->messages($references));
        $this->assertSame([true, true, true], array_map(fn (MessageReference $r) => $r->isDynamic(), $references));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function dynamicMessagesProvider(): array
    {
        return [
            'variable' => ["{{ text|trans }}"],
            'attribute' => ["{{ item.title|trans }}"],
            'concatenation' => ["{{ ('Hello ' ~ name)|trans }}"],
            'condition' => ["{{ (flag ? 'A' : 'B')|trans }}"],
            'function' => ["{{ t(text) }}"],
        ];
    }

    #[DataProvider('dynamicMessagesProvider')]
    public function testAMessageThatIsNotALiteralIsReportedAsDynamic(string $source): void
    {
        $references = $this->scan($source);

        $this->assertCount(1, $references);
        $this->assertNull($references[0]->id);
        $this->assertTrue($references[0]->isDynamic());
    }

    public function testADynamicMessageHasTheTemplateAndTheLineThatCallsIt(): void
    {
        $reference = $this->scan("<p>text</p>\n  <b>{{ text|trans }}</b>")[0];

        $this->assertSame('t.twig: <b>{{ text|trans }}</b>', $reference->identity());
    }

    public function testALiteralIsNotDynamic(): void
    {
        $this->assertFalse($this->scan("{{ 'A'|trans }}")[0]->isDynamic());
    }

    public function testItIgnoresWhatOnlyLooksLikeACall(): void
    {
        $references = $this->scan(
            "{# {{ 'in_a_comment'|trans }} #}'in_the_text'|trans{{ \"'in_a_string'|trans\" }}"
            . "{% verbatim %}{{ 'in_verbatim'|trans }}{% endverbatim %}"
        );

        $this->assertSame([], $references);
    }

    public function testItFindsTheMessagesWhereverTwigAllowsACall(): void
    {
        $references = $this->scan(<<<'TWIG'
            {% macro m() %}{{ 'in_macro'|trans }}{% endmacro %}
            {% set a = 'in_set'|trans %}
            {% for i in [1] %}{{ 'in_for'|trans }}{% endfor %}
            {% if true %}{{ 'in_if'|trans }}{% else %}{{ 'in_else'|trans }}{% endif %}
            {{ 'x'|replace({'x': 'in_filter'|trans}) }}
            {{ 'outer'|trans({'p': t('inner')}) }}
            TWIG);

        $ids = array_map(fn (MessageReference $r) => $r->id, $references);
        sort($ids);

        $this->assertSame(
            ['in_else', 'in_filter', 'in_for', 'in_if', 'in_macro', 'in_set', 'inner', 'outer'],
            $ids
        );
    }

    public function testItDoesNotTakeAnotherFilterOrFunctionForTheTranslation(): void
    {
        $twig = $this->environment("{{ 'A'|trans }}{{ 'B'|upper }}{{ lower('C') }}");
        $twig->addFilter(new \Twig\TwigFilter('upper', fn (string $text) => strtoupper($text)));
        $twig->addFunction(new \Twig\TwigFunction('lower', fn (string $text) => strtolower($text)));

        $references = (new TemplateMessageScanner($twig))->scanTemplate('t.twig');

        $this->assertSame([['A', 'messages']], $this->messages($references));
    }

    public function testItTellsTheTranslationByWhatItIsAndNotByItsName(): void
    {
        $twig = new Environment(new ArrayLoader(['t.twig' => "{{ 'A'|trans }}"]));
        $twig->addExtension(new TranslationExtension());
        $twig->addExtension(new class () extends \Twig\Extension\AbstractExtension {
            public function getFilters(): array
            {
                return [new \Twig\TwigFilter('other', fn (string $text) => $text)];
            }
        });

        $this->assertSame([['A', 'messages']], $this->messages((new TemplateMessageScanner($twig))->scanTemplate('t.twig')));
    }

    public function testATemplateThatCanNotBeParsedIsNotHidden(): void
    {
        $this->expectException(SyntaxError::class);

        $this->scan("{% trans %}{{ not_plain }}{% endtrans %}");
    }

    public function testItNeedsTheTranslationExtension(): void
    {
        $this->expectException(LogicException::class);

        (new TemplateMessageScanner(new Environment(new ArrayLoader(['t.twig' => 'text']))))->scanTemplate('t.twig');
    }

    public function testWhatTFunctionMadeAndTheFilterTranslatesIsOneMessage(): void
    {
        $references = $this->scan(
            "{{ t('Close', {}, 'a')|trans }}{{ t('Open')|trans({}, 'other') }}{{ t('Save')|trans }}"
        );

        $this->assertSame([['Close', 'a'], ['Open', 'messages'], ['Save', 'messages']], $this->messages($references));
    }

    public function testAFunctionThatIsNotALiteralIsOneDynamicMessageAlsoWithTheFilter(): void
    {
        $references = $this->scan("{{ t(text)|trans }}");

        $this->assertCount(1, $references);
        $this->assertTrue($references[0]->isDynamic());
    }

    public function testTheLineIsTheOneOfTheFileAlsoWithComponents(): void
    {
        $twig = $this->componentsEnvironment([
            't.html.twig' => "<p>one</p>\n<twig:block-alert\n    content=\"A\"\n    type=\"info\"\n/>\n<p>{{ 'Hello'|trans }}</p>\n<p>{{ text|trans }}</p>",
        ]);

        $references = (new TemplateMessageScanner($twig))->scanTemplate('t.html.twig');

        $this->assertSame(['Hello', null], array_map(fn (MessageReference $r) => $r->id, $references));
        $this->assertSame([6, 7], array_map(fn (MessageReference $r) => $r->line, $references));
        $this->assertSame('t.html.twig: <p>{{ text|trans }}</p>', $references[1]->identity());
    }

    public function testAMessageThatTwigCopiesIsFoundOnce(): void
    {
        $references = $this->scan("{{ name|default('Hello'|trans) }}\n{{ name|default('Hello'|trans) }}");

        $this->assertSame([['Hello', 'messages'], ['Hello', 'messages']], $this->messages($references));
        $this->assertSame([1, 2], array_map(fn (MessageReference $r) => $r->line, $references));
    }

    public function testItScansADirectoryInOrderOfTemplate(): void
    {
        $directory = sys_get_temp_dir() . '/derafu-twig-messages-' . bin2hex(random_bytes(4));
        mkdir($directory . '/sub', 0777, true);
        $this->directories[] = $directory;
        file_put_contents($directory . '/b.twig', "{{ 'B'|trans }}");
        file_put_contents($directory . '/a.twig', "{{ 'A'|trans }}");
        file_put_contents($directory . '/sub/c.twig', "{{ 'C'|trans }}");
        file_put_contents($directory . '/ignored.txt', "{{ 'D'|trans }}");

        $twig = new Environment(new FilesystemLoader($directory));
        $twig->addExtension(new TranslationExtension());

        $references = (new TemplateMessageScanner($twig))->scanDirectory($directory);

        $this->assertSame(['A', 'B', 'C'], array_map(fn (MessageReference $r) => $r->id, $references));
        $this->assertSame(
            [realpath($directory) . '/a.twig', realpath($directory) . '/b.twig', realpath($directory) . '/sub/c.twig'],
            array_map(fn (MessageReference $r) => $r->file, $references)
        );
    }

    public function testADirectoryThatDoesNotExistIsNotHidden(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new TemplateMessageScanner($this->environment('')))->scanDirectory('/does/not/exist');
    }
}
