<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
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
}
