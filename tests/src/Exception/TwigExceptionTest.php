<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Exception;

use Derafu\Translation\Contract\TranslatableInterface;
use Derafu\Twig\Exception\TwigException;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * The exceptions of the package can be translated, like the ones of the rest of
 * the packages.
 */
#[CoversClass(TwigException::class)]
final class TwigExceptionTest extends TestCase
{
    public function testTheExceptionIsTranslatable(): void
    {
        $exception = new TwigException('Failed.');

        $this->assertInstanceOf(Exception::class, $exception);
        $this->assertInstanceOf(TranslatableInterface::class, $exception);
        $this->assertSame('Failed.', $exception->getMessage());
    }
}
