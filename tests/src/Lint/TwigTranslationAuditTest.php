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

use Derafu\Translation\Contract\TranslationResourceProviderInterface;
use Derafu\Twig\Exception\TwigException;
use Derafu\Twig\Extension\TranslationExtension;
use Derafu\Twig\Lint\TemplateFinder;
use Derafu\Twig\Lint\TemplateMessageScanner;
use Derafu\Twig\Lint\TemplateSource;
use Derafu\Twig\Lint\TemplateText;
use Derafu\Twig\Lint\TemplateTextScanner;
use Derafu\Twig\Lint\TwigTranslationAudit;
use Derafu\Twig\Lint\TwigTranslationAuditReport;
use Derafu\Twig\Node\TransDefaultDomainNode;
use Derafu\Twig\Node\TransNode;
use Derafu\Twig\NodeVisitor\TranslationDefaultDomainNodeVisitor;
use Derafu\Twig\TokenParser\TransDefaultDomainTokenParser;
use Derafu\Twig\TokenParser\TransTokenParser;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Checks a package with templates in one call: its code, like the audit of
 * `derafu/translation` does, and its templates, against the same catalogues.
 *
 * It only finds facts. The tests say what is found, in the lists of the report.
 */
#[CoversClass(TwigTranslationAudit::class)]
#[CoversClass(TwigTranslationAuditReport::class)]
#[UsesClass(TemplateMessageScanner::class)]
#[UsesClass(TemplateTextScanner::class)]
#[UsesClass(TemplateText::class)]
#[UsesClass(TemplateFinder::class)]
#[UsesClass(TemplateSource::class)]
#[UsesClass(TwigException::class)]
#[UsesClass(TranslationExtension::class)]
#[UsesClass(TransNode::class)]
#[UsesClass(TransDefaultDomainNode::class)]
#[UsesClass(TranslationDefaultDomainNodeVisitor::class)]
#[UsesClass(TransTokenParser::class)]
#[UsesClass(TransDefaultDomainTokenParser::class)]
final class TwigTranslationAuditTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = (string) realpath(sys_get_temp_dir()) . '/derafu-twig-audit-' . bin2hex(random_bytes(4));
        mkdir($this->root . '/src', 0777, true);
        mkdir($this->root . '/templates/sub', 0777, true);
        mkdir($this->root . '/translations', 0777, true);
    }

    protected function tearDown(): void
    {
        $this->remove($this->root);
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
     * @param array<string, string> $messages The catalogue of each domain.
     */
    private function catalogue(string $domain, array $messages): void
    {
        file_put_contents(
            $this->root . '/translations/' . $domain . '+intl-icu.es.php',
            '<?php return ' . var_export($messages, true) . ';'
        );
    }

    private function code(string $code): void
    {
        file_put_contents($this->root . '/src/code.php', "<?php\n\nuse Derafu\\Twig\\Exception\\TwigException;\n\n" . $code);
    }

    private function template(string $name, string $source): void
    {
        file_put_contents($this->root . '/templates/' . $name, $source);
    }

    private function provider(): TranslationResourceProviderInterface
    {
        return new class ($this->root . '/translations') implements TranslationResourceProviderInterface {
            public function __construct(private readonly string $directory)
            {
            }

            public function getDirectories(): iterable
            {
                return [$this->directory];
            }
        };
    }

    /**
     * @param TranslationResourceProviderInterface|iterable<TranslationResourceProviderInterface>|null $provider
     * @param list<string> $allowedTexts
     */
    private function audit(
        TranslationResourceProviderInterface|iterable|null $provider = null,
        ?string $defaultDomain = null,
        array $allowedTexts = []
    ): TwigTranslationAuditReport {
        $twig = new Environment(new FilesystemLoader($this->root . '/templates'));
        $twig->addExtension(new TranslationExtension());

        return (new TwigTranslationAudit())->audit(
            $this->root . '/src',
            $this->root . '/templates',
            $provider ?? $this->provider(),
            $twig,
            defaultDomain: $defaultDomain,
            allowedTexts: $allowedTexts
        );
    }

    private function complete(): void
    {
        $this->code("throw new TwigException('Not found.');\n");
        $this->template('a.html.twig', "{% trans_default_domain 'twig+intl-icu' %}<p>{{ 'Hello'|trans }}</p>");
        $this->catalogue('errors', ['Not found.' => 'No encontrado.']);
        $this->catalogue('twig', ['Hello' => 'Hola']);
    }

    public function testAPackageThatIsTranslatedHasNothingToReport(): void
    {
        $this->complete();

        $report = $this->audit();

        $this->assertFalse($report->nothingFound);
        $this->assertSame([], $report->describe($report->dynamicMessages));
        $this->assertSame([], $report->describe($report->missingTranslations));
        $this->assertSame([], $report->describe($report->notUsedBySources));
        $this->assertSame([], $report->describe($report->notTranslatable));
        $this->assertSame([], $report->describe($report->untranslatedTexts));
    }

    public function testAMessageOfATemplateThatHasNoEntryIsReported(): void
    {
        $this->complete();
        $this->template('sub/b.html.twig', "\n{{ 'Missing'|trans({}, 'twig+intl-icu') }}{{ 'Other'|trans({}, 'unknown') }}");

        $report = $this->audit();

        $this->assertSame(
            [
                'sub/b.html.twig:2 "Missing" [twig+intl-icu]',
                'sub/b.html.twig:2 "Other" [unknown]',
            ],
            $report->describe($report->missingTranslations)
        );
    }

    public function testAMessageOfTheCodeThatHasNoEntryIsStillReported(): void
    {
        $this->complete();
        $this->code("throw new TwigException('Not found.');\nthrow new TwigException('Another.');\n");

        $report = $this->audit();

        $this->assertSame(['code.php:6 "Another." [errors]'], $report->describe($report->missingTranslations));
    }

    public function testTheMessagesOfTheCodeAndOfTheTemplatesAreReportedTogether(): void
    {
        $this->code("throw new TwigException('Code missing.');\n");
        $this->template('a.html.twig', "{{ 'Template missing'|trans({}, 'twig') }}");

        $report = $this->audit();

        $this->assertSame(
            ['code.php:5 "Code missing." [errors]', 'a.html.twig:1 "Template missing" [twig]'],
            $report->describe($report->missingTranslations)
        );
    }

    public function testAnEntryThatOnlyATemplateUsesIsNotLeftOver(): void
    {
        $this->complete();

        $this->assertSame([], $this->audit()->notUsedBySources);
    }

    public function testTheDomainWithOrWithoutTheSuffixOfIcuIsTheSame(): void
    {
        $this->code("throw new TwigException('Not found.');\n");
        $this->template('a.html.twig', "{{ 'One'|trans({}, 'twig') }}{{ 'Two'|trans({}, 'twig+intl-icu') }}");
        $this->catalogue('errors', ['Not found.' => 'No encontrado.']);
        $this->catalogue('twig', ['One' => 'Uno', 'Two' => 'Dos']);

        $report = $this->audit();

        $this->assertSame([], $report->describe($report->missingTranslations));
        $this->assertSame([], $report->describe($report->notUsedBySources));
    }

    public function testAnEntryThatNothingUsesIsReported(): void
    {
        $this->complete();
        $this->catalogue('twig', ['Hello' => 'Hola', 'Unused' => 'Sin uso']);
        $this->catalogue('errors', ['Not found.' => 'No encontrado.', 'Unused too.' => 'Tampoco.']);

        $report = $this->audit();

        $this->assertSame(['"Unused too." [errors]', '"Unused" [twig]'], $report->describe($report->notUsedBySources));
    }

    public function testAnEntryIsNotUsedByATemplateOfAnotherDomain(): void
    {
        $this->complete();
        $this->template('b.html.twig', "{{ 'Hello'|trans({}, 'other') }}");
        $this->catalogue('other', ['Hello' => 'Hola otro']);
        $this->catalogue('twig', ['Hello' => 'Hola', 'Bye' => 'Chao']);

        $report = $this->audit();

        $this->assertSame(['"Bye" [twig]'], $report->describe($report->notUsedBySources));
    }

    public function testTheMessagesThatDoNotHaveADomainUseTheOneOfTheEnvironment(): void
    {
        $this->code("throw new TwigException('Not found.');\n");
        $this->template('a.html.twig', "{{ 'Hello'|trans }}");
        $this->catalogue('errors', ['Not found.' => 'No encontrado.']);
        $this->catalogue('app', ['Hello' => 'Hola']);

        $report = $this->audit(defaultDomain: 'app');

        $this->assertSame([], $report->describe($report->missingTranslations));
        $this->assertSame([], $report->describe($report->notUsedBySources));
    }

    public function testAMessageThatIsNotALiteralIsReportedAsDynamic(): void
    {
        $this->complete();
        $this->template('b.html.twig', "<p>x</p>\n  {{ text|trans }}\n");

        $report = $this->audit();

        $this->assertSame(['b.html.twig:2 b.html.twig: {{ text|trans }}'], $report->describe($report->dynamicMessages));
    }

    public function testTheDynamicMessagesOfTheCodeAreStillReported(): void
    {
        $this->complete();
        $this->code("throw new TwigException('Not found.');\n\$message = 'x';\nthrow new TwigException(\$message);\n");

        $report = $this->audit();

        $this->assertCount(1, $report->dynamicMessages);
        $this->assertStringStartsWith('code.php:7 ', $report->describe($report->dynamicMessages)[0]);
    }

    public function testAnExceptionThatIsNotTranslatableIsStillReported(): void
    {
        $this->complete();
        $this->code("throw new TwigException('Not found.');\nthrow new \\RuntimeException('Native.');\n");

        $report = $this->audit();

        $this->assertSame(['code.php:6 RuntimeException'], $report->describe($report->notTranslatable));
    }

    public function testATextThatDoesNotGoThroughTheTranslationIsReported(): void
    {
        $this->complete();
        $this->template('b.html.twig', "<p>Hello</p>\n<button aria-label=\"Close\">Save {{ x }}</button>");

        $report = $this->audit();

        $this->assertSame(
            ['b.html.twig:1 "Hello" [text]', 'b.html.twig:2 "Close" [aria-label]', 'b.html.twig:2 "Save" [text]'],
            $report->describe($report->untranslatedTexts)
        );
    }

    public function testATextThatIsAllowedIsNotReported(): void
    {
        $this->complete();
        $this->template('b.html.twig', '<p>Derafu</p><p>Hello</p>');

        $report = $this->audit(allowedTexts: ['Derafu']);

        $this->assertSame(['b.html.twig:1 "Hello" [text]'], $report->describe($report->untranslatedTexts));
    }

    public function testThereIsSomethingFoundIfOnlyTheTemplatesHaveMessages(): void
    {
        $this->code("function nothing() {}\n");
        $this->template('a.html.twig', "{{ 'Hello'|trans({}, 'twig') }}");
        $this->catalogue('twig', ['Hello' => 'Hola']);

        $this->assertFalse($this->audit()->nothingFound);
    }

    public function testThereIsNothingFoundIfNeitherTheCodeNorTheTemplatesHaveMessages(): void
    {
        $this->code("function nothing() {}\n");
        $this->template('a.html.twig', '<p>{{ x }}</p>');

        $this->assertTrue($this->audit()->nothingFound);
    }

    public function testTheCataloguesCanBeGivenAsAnIterable(): void
    {
        $this->complete();

        $providers = (function () {
            yield $this->provider();
        })();

        $report = $this->audit($providers);

        $this->assertSame([], $report->describe($report->missingTranslations));
        $this->assertSame([], $report->describe($report->notUsedBySources));
    }

    public function testAnExceptionOfTheCodeCanBeAllowed(): void
    {
        $this->complete();
        $this->code("throw new TwigException('Not found.');\nthrow new \\RuntimeException('Native.');\n");

        $twig = new Environment(new FilesystemLoader($this->root . '/templates'));
        $twig->addExtension(new TranslationExtension());

        $report = (new TwigTranslationAudit())->audit(
            $this->root . '/src',
            $this->root . '/templates',
            $this->provider(),
            $twig,
            allowedThrowables: [\RuntimeException::class]
        );

        $this->assertSame([], $report->notTranslatable);
    }

    public function testADirectoryThatDoesNotExistIsNotHidden(): void
    {
        $this->complete();

        $twig = new Environment(new FilesystemLoader($this->root . '/templates'));
        $twig->addExtension(new TranslationExtension());

        $this->expectException(InvalidArgumentException::class);

        (new TwigTranslationAudit())->audit($this->root . '/src', $this->root . '/no-templates', $this->provider(), $twig);
    }

    public function testTheReportKeepsWhatWasAudited(): void
    {
        $this->complete();

        $report = $this->audit();

        $this->assertSame($this->root . '/src', $report->directory);
        $this->assertSame($this->root . '/templates', $report->templates);
    }
}
