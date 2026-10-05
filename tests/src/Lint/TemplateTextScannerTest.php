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
use Derafu\Twig\Lint\TemplateFinder;
use Derafu\Twig\Lint\TemplateSource;
use Derafu\Twig\Lint\TemplateText;
use Derafu\Twig\Lint\TemplateTextScanner;
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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;

/**
 * Finds the texts that the templates write and that a person can read (the text
 * between the tags of the HTML and the attributes that are shown), so they can be
 * checked to go through the translation.
 *
 * It only finds texts: whether one has to be translated is for whoever uses it to
 * decide.
 */
#[CoversClass(TemplateTextScanner::class)]
#[CoversClass(TemplateText::class)]
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
final class TemplateTextScannerTest extends TestCase
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

    /**
     * @return list<TemplateText>
     */
    private function scan(string $source): array
    {
        $twig = new Environment(new ArrayLoader(['t.twig' => $source]));
        $twig->addExtension(new TranslationExtension());

        return (new TemplateTextScanner($twig))->scanTemplate('t.twig');
    }

    /**
     * @return list<array{string, string}>
     */
    private function texts(string $source): array
    {
        return array_map(fn (TemplateText $t) => [$t->text, $t->kind], $this->scan($source));
    }

    public function testItFindsTheTextBetweenTheTags(): void
    {
        $this->assertSame(
            [['Hello', 'text'], ['World', 'text']],
            $this->texts('<p>Hello</p><b> World </b>')
        );
    }

    public function testItFindsTheAttributesThatAPersonReads(): void
    {
        $this->assertSame(
            [['Close', 'aria-label'], ['A logo', 'alt'], ['Tip', 'title'], ['Search', 'placeholder']],
            $this->texts('<button aria-label="Close"><img alt="A logo" TITLE=\'Tip\'><input placeholder="Search"></button>')
        );
    }

    public function testItIgnoresTheAttributesThatAPersonDoesNotRead(): void
    {
        $this->assertSame([], $this->texts('<a href="https://example.com/path" class="btn btn-primary" id="main" data-bs-toggle="modal" data-x="Some words" aria-labelledby="the-label"></a>'));
    }

    public function testItIgnoresWhatIsNotAText(): void
    {
        $this->assertSame([], $this->texts('<p>  | — © 2026 &nbsp; 123 </p>'));
    }

    public function testItReadsLettersOfAnyAlphabet(): void
    {
        $this->assertSame([['Ñandú', 'text'], ['Привет', 'text'], ['日本語', 'text']], $this->texts('<p>Ñandú</p><p>Привет</p><p>日本語</p>'));
    }

    public function testItDecodesTheEntitiesOfTheText(): void
    {
        $this->assertSame([['Tom & Jerry’s', 'text']], $this->texts('<p>Tom &amp; Jerry&rsquo;s</p>'));
    }

    public function testWhatATemplatePrintsIsNotAText(): void
    {
        $this->assertSame([], $this->texts('<p>{{ title }}</p><img alt="{{ title }}"><i>{% if x %}{{ y }}{% endif %}</i>'));
    }

    public function testTheTextAroundWhatATemplatePrintsIsAText(): void
    {
        $this->assertSame(
            [['Slide', 'aria-label'], ['Hello , welcome', 'text'], ['Background', 'alt']],
            $this->texts('<i aria-label="Slide {{ loop.index }}"></i><p>Hello {{ name }}, welcome</p><img alt="Background {{ loop.index }}">')
        );
    }

    public function testATextThatGoesThroughTheTranslationIsNotFound(): void
    {
        $this->assertSame([], $this->texts(
            "<p>{{ 'Hello'|trans }}</p><i aria-label=\"{{ 'Close'|trans }}\"></i>{% trans %}Plain{% endtrans %}"
        ));
    }

    public function testATextAroundATranslationIsFound(): void
    {
        $this->assertSame(
            [['Close', 'text']],
            $this->texts("<p>Close {{ 'Hello'|trans }}</p>")
        );
    }

    public function testTheTextOfAnAttributeThatTwigPutsAroundATagIsFound(): void
    {
        $this->assertSame(
            [['Close', 'aria-label']],
            $this->texts('<button {% if x %}class="a"{% endif %} aria-label="Close"></button>')
        );
    }

    public function testItDoesNotReadTheScriptsTheStylesAndTheComments(): void
    {
        $this->assertSame(
            [['Visible', 'text']],
            $this->texts(
                '<script type="text/javascript">var a = "Hidden <b>text</b>";</script>'
                . '<style>.a::after { content: "Hidden"; }</style>'
                . '<!-- Hidden comment <p>Hidden</p> -->'
                . "{# Hidden twig comment #}<p>Visible</p>"
            )
        );
    }

    public function testItReadsTheTextAfterAScriptWithAnAttribute(): void
    {
        $this->assertSame([['After', 'text']], $this->texts('<script src="a.js" async></script><p>After</p>'));
    }

    public function testItJoinsTheTextThatTwigSplitsAroundAComment(): void
    {
        $this->assertSame([['Hello world', 'text']], $this->texts('<p>Hello {# note #}world</p>'));
    }

    public function testATextThatIsSplitByATagOfTwigAreTwoTexts(): void
    {
        $this->assertSame(
            [['A', 'text'], ['B', 'text']],
            $this->texts('{% if x %}<p>A</p>{% endif %}{% if y %}<p>B</p>{% endif %}')
        );
    }

    public function testTheTextOfABranchOfTwigIsFound(): void
    {
        $this->assertSame(
            [['One', 'text'], ['Two', 'text'], ['Each', 'text']],
            $this->texts('{% if x %}<p>One</p>{% else %}<p>Two</p>{% endif %}{% for i in l %}<li>Each</li>{% endfor %}')
        );
    }

    public function testALessThanSignThatIsNotATagIsPartOfTheText(): void
    {
        $this->assertSame([['a < b', 'text']], $this->texts('<p>a < b</p>'));
    }

    public function testItReportsTheLineWhereEachTextStarts(): void
    {
        $texts = $this->scan("<div>\n  <p>One</p>\n\n  <img\n    alt=\"Two\">\n  Three\n{{ x }}\n<i>Four</i></div>");

        $this->assertSame(
            [['One', 2], ['Two', 5], ['Three', 6], ['Four', 8]],
            array_map(fn (TemplateText $t) => [$t->text, $t->line], $texts)
        );
    }

    public function testItReportsTheLineOfATextAfterWhatATemplatePrints(): void
    {
        $texts = $this->scan("{{ x }}\n{{ y }} Hello\n<p>\n\n{{ z }} World</p>");

        $this->assertSame(
            [['Hello', 2], ['World', 5]],
            array_map(fn (TemplateText $t) => [$t->text, $t->line], $texts)
        );
    }

    public function testTheTextsAreInOrderOfLine(): void
    {
        $texts = $this->scan("<p>A</p>\n<img alt=\"B\">\n<p>C</p>");

        $this->assertSame(['A', 'B', 'C'], array_map(fn (TemplateText $t) => $t->text, $texts));
    }

    public function testTheAttributesThatAreReadCanBeChosen(): void
    {
        $twig = new Environment(new ArrayLoader(['t.twig' => '<i data-x="Some words" alt="Hidden"></i>']));
        $twig->addExtension(new TranslationExtension());

        $texts = (new TemplateTextScanner($twig, ['data-x']))->scanTemplate('t.twig');

        $this->assertSame([['Some words', 'data-x']], array_map(fn (TemplateText $t) => [$t->text, $t->kind], $texts));
    }

    /**
     * @return array<string, array{string, list<array{string, string}>}>
     */
    public static function brokenHtmlProvider(): array
    {
        return [
            'tag that is not closed' => ['<p>Hello</p><img alt="Close"', [['Hello', 'text'], ['Close', 'alt']]],
            'quote that is not closed' => ['<img alt="Close><p>After</p>', [['"Close>', 'text'], ['After', 'text']]],
            'comment that is not closed' => ['<p>Before</p><!-- never closed', [['Before', 'text']]],
            'script that is not closed' => ['<p>Before</p><script>var a = 1;', [['Before', 'text']]],
            'empty' => ['', []],
        ];
    }

    /**
     * @param list<array{string, string}> $expected
     */
    #[DataProvider('brokenHtmlProvider')]
    public function testHtmlThatIsBrokenIsReadAsFarAsItCanBe(string $source, array $expected): void
    {
        $this->assertSame($expected, $this->texts($source));
    }

    public function testATemplateThatCanNotBeParsedIsNotHidden(): void
    {
        $this->expectException(SyntaxError::class);

        $this->scan('{% trans %}{{ not_plain }}{% endtrans %}');
    }

    public function testTheLineIsTheOneOfTheFileAlsoWithComponents(): void
    {
        $twig = $this->componentsEnvironment([
            't.html.twig' => "<p>one</p>\n<twig:block-alert\n    content=\"A\"\n    type=\"info\"\n/>\n<p>two</p>\n{{ x }}\n<i aria-label=\"three\"></i>",
        ]);

        $texts = (new TemplateTextScanner($twig))->scanTemplate('t.html.twig');

        $this->assertSame(
            [['one', 1], ['two', 6], ['three', 8]],
            array_map(fn (TemplateText $t) => [$t->text, $t->line], $texts)
        );
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function provideTemplateNames(): array
    {
        return [
            'html' => ['page.html.twig', true],
            'pdf' => ['page.pdf.twig', true],
            'format in capitals' => ['page.HTML.twig', true],
            'without format' => ['page.twig', true],
            'a name with dots and html' => ['my.page.html.twig', true],
            'markdown' => ['page.md.twig', false],
            'xml' => ['feed.xml.twig', false],
            'text' => ['mail.txt.twig', false],
            'a name with dots and markdown' => ['my.page.md.twig', false],
        ];
    }

    #[DataProvider('provideTemplateNames')]
    public function testOnlyTheTemplatesOfAnHtmlFormatAreRead(string $name, bool $read): void
    {
        $twig = new Environment(new ArrayLoader([$name => '<p>Hello</p>']));
        $twig->addExtension(new TranslationExtension());

        $this->assertSame($read ? ['Hello'] : [], array_map(fn (TemplateText $t) => $t->text, (new TemplateTextScanner($twig))->scanTemplate($name)));
    }

    public function testTheFormatsThatAreReadCanBeChosen(): void
    {
        $twig = new Environment(new ArrayLoader(['a.html.twig' => '<p>Html</p>', 'b.md.twig' => '<p>Markdown</p>']));
        $twig->addExtension(new TranslationExtension());
        $scanner = new TemplateTextScanner($twig, formats: ['md']);

        $this->assertSame([], $scanner->scanTemplate('a.html.twig'));
        $this->assertSame(['Markdown'], array_map(fn (TemplateText $t) => $t->text, $scanner->scanTemplate('b.md.twig')));
    }

    public function testItScansADirectoryInOrderOfTemplate(): void
    {
        $directory = sys_get_temp_dir() . '/derafu-twig-texts-' . bin2hex(random_bytes(4));
        mkdir($directory . '/sub', 0777, true);
        $this->directories[] = $directory;
        file_put_contents($directory . '/b.twig', '<p>B</p>');
        file_put_contents($directory . '/a.twig', '<p>A</p>');
        file_put_contents($directory . '/sub/c.twig', '<p>C</p>');
        file_put_contents($directory . '/ignored.txt', '<p>D</p>');
        file_put_contents($directory . '/skipped.md.twig', '<p>E</p>');

        $twig = new Environment(new FilesystemLoader($directory));
        $twig->addExtension(new TranslationExtension());

        $texts = (new TemplateTextScanner($twig))->scanDirectory($directory);

        $this->assertSame(['A', 'B', 'C'], array_map(fn (TemplateText $t) => $t->text, $texts));
        $this->assertSame(['a.twig', 'b.twig', 'sub/c.twig'], array_map(fn (TemplateText $t) => $t->template, $texts));
        $this->assertSame(
            [realpath($directory) . '/a.twig', realpath($directory) . '/b.twig', realpath($directory) . '/sub/c.twig'],
            array_map(fn (TemplateText $t) => $t->file, $texts)
        );
    }
}
