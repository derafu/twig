<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Provider;

use Derafu\Translation\Contract\TranslatableInterface;
use Derafu\Twig\Provider\DirectoryComponentProvider;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * A directory of components that does not exist is a translatable error.
 */
#[CoversClass(DirectoryComponentProvider::class)]
final class DirectoryComponentProviderTest extends TestCase
{
    public function testADirectoryThatDoesNotExistIsATranslatableError(): void
    {
        $exception = null;
        try {
            new DirectoryComponentProvider('/does/not/exist');
        } catch (Throwable $e) {
            $exception = $e;
        }

        $this->assertInstanceOf(InvalidArgumentException::class, $exception);
        $this->assertInstanceOf(TranslatableInterface::class, $exception);
        $this->assertSame('Directory "/does/not/exist" does not exist.', $exception->getMessage());
    }
}
