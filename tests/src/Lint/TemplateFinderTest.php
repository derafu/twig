<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Lint;

use Derafu\Translation\Contract\TranslatableInterface;
use Derafu\Twig\Lint\TemplateFinder;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * Finds the templates of a directory, by their name from it.
 */
#[CoversClass(TemplateFinder::class)]
final class TemplateFinderTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/derafu-twig-finder-' . bin2hex(random_bytes(4));
        mkdir($this->directory . '/sub/deep', 0777, true);
        foreach (['b.html.twig', 'a.html.twig', 'sub/c.html.twig', 'sub/deep/d.html.twig', 'notes.txt', 'sub/page.md'] as $file) {
            file_put_contents($this->directory . '/' . $file, 'x');
        }
    }

    protected function tearDown(): void
    {
        foreach (['sub/deep/d.html.twig', 'sub/c.html.twig', 'sub/page.md', 'a.html.twig', 'b.html.twig', 'notes.txt'] as $file) {
            unlink($this->directory . '/' . $file);
        }
        rmdir($this->directory . '/sub/deep');
        rmdir($this->directory . '/sub');
        rmdir($this->directory);
    }

    public function testItFindsTheTemplatesInOrderWithTheirNameFromTheDirectory(): void
    {
        $this->assertSame(
            ['a.html.twig', 'b.html.twig', 'sub/c.html.twig', 'sub/deep/d.html.twig'],
            (new TemplateFinder())->find($this->directory)
        );
    }

    public function testItFindsTheFilesOfTheExtensionThatWasAsked(): void
    {
        $this->assertSame(['sub/page.md'], (new TemplateFinder())->find($this->directory, 'md'));
    }

    public function testADirectoryThatDoesNotExistIsATranslatableError(): void
    {
        $exception = null;
        try {
            (new TemplateFinder())->find('/does/not/exist');
        } catch (Throwable $e) {
            $exception = $e;
        }

        $this->assertInstanceOf(InvalidArgumentException::class, $exception);
        $this->assertInstanceOf(TranslatableInterface::class, $exception);
        $this->assertSame('The directory /does/not/exist does not exist.', $exception->getMessage());
    }
}
