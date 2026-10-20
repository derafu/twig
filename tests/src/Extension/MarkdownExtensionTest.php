<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Extension;

use Derafu\Translation\Contract\TranslatableInterface;
use Derafu\Twig\Extension\MarkdownExtension;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;
use Throwable;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * Rendering Markdown without its service is a translatable error.
 */
#[CoversClass(MarkdownExtension::class)]
final class MarkdownExtensionTest extends TestCase
{
    public function testRenderingWithoutTheMarkdownServiceIsATranslatableError(): void
    {
        // The constructor creates the service when the package is installed:
        // without running it, the service is not there.
        $extension = (new ReflectionClass(MarkdownExtension::class))->newInstanceWithoutConstructor();

        $exception = null;
        try {
            $extension->renderMarkdown(new Environment(new ArrayLoader()), [], '# Title');
        } catch (Throwable $e) {
            $exception = $e;
        }

        $this->assertInstanceOf(LogicException::class, $exception);
        $this->assertInstanceOf(TranslatableInterface::class, $exception);
        $this->assertStringStartsWith('MarkdownExtension requires Derafu\Markdown to be installed.', $exception->getMessage());
    }

    public function testContentThatPcreCanNotProcessIsATranslatableError(): void
    {
        $extension = new MarkdownExtension();

        // A tag that is not closed, followed by a very large text: the search of
        // the tags exhausts the backtrack limit of PCRE.
        $content = '<twig:block-alert content="hello" ' . str_repeat('word ', 300000);

        $exception = null;
        try {
            $extension->renderMarkdown(new Environment(new ArrayLoader()), [], $content, true);
        } catch (Throwable $e) {
            $exception = $e;
        }

        $this->assertInstanceOf(RuntimeException::class, $exception);
        $this->assertInstanceOf(TranslatableInterface::class, $exception);
        $this->assertStringStartsWith('The Markdown content could not be processed: ', $exception->getMessage());
    }

    private function twig(): Environment
    {
        $twig = new Environment(new ArrayLoader([
            'plain' => '{{ md|markdown }}',
            'twig' => '{{ md|markdown(true) }}',
        ]));
        $twig->addExtension(new MarkdownExtension());

        return $twig;
    }

    public function testMarkdownIsRenderedAsHtml(): void
    {
        $html = $this->twig()->render('plain', ['md' => "# Title\n\nText with {{ name }}"]);

        $this->assertStringContainsString('<h1', $html);
        $this->assertStringContainsString('Title', $html);
        // Without the option the Twig code is text.
        $this->assertStringContainsString('{{ name }}', $html);
    }

    public function testTheTwigOfTheMarkdownIsRenderedWithTheContext(): void
    {
        $html = $this->twig()->render('twig', ['md' => "# Title\n\nHi {{ name }}", 'name' => 'World']);

        $this->assertStringContainsString('Hi World', $html);
        $this->assertStringNotContainsString('{{ name }}', $html);
    }

    public function testTheTagsOfComponentsSurviveTheMarkdown(): void
    {
        $markdown = "Text\n\n<twig:block-alert content=\"A\" type='info' />\n\n<twig:block-alert content=\"B\" />\n";

        $html = $this->twig()->render('twig', ['md' => $markdown]);

        // The parser does not escape them or change their quotes.
        $this->assertStringContainsString('<twig:block-alert content="A" type=\'info\' />', $html);
        $this->assertStringContainsString('<twig:block-alert content="B" />', $html);
    }
}
