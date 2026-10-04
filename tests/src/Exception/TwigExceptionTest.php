<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Exception;

use Derafu\Translation\Contract\TranslatableInterface;
use Derafu\Twig\Exception\TwigComponentException;
use Derafu\Twig\Exception\TwigException;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * The exceptions of the package can be translated, like the ones of the rest of
 * the packages.
 */
#[CoversClass(TwigException::class)]
#[CoversClass(TwigComponentException::class)]
final class TwigExceptionTest extends TestCase
{
    public function testTheExceptionsAreTranslatable(): void
    {
        foreach ([new TwigException('Failed.'), new TwigComponentException('Failed.')] as $exception) {
            $this->assertInstanceOf(Exception::class, $exception);
            $this->assertInstanceOf(TranslatableInterface::class, $exception);
            $this->assertSame('Failed.', $exception->getMessage());
        }
    }

    public function testTheComponentExceptionIsATwigException(): void
    {
        $this->assertInstanceOf(TwigException::class, new TwigComponentException('Failed.'));
    }
}
