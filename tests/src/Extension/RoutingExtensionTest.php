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

use Derafu\Routing\Contract\RouterInterface;
use Derafu\Translation\Contract\TranslatableInterface;
use Derafu\Twig\Extension\RoutingExtension;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * A router without a context is a translatable error.
 */
#[CoversClass(RoutingExtension::class)]
final class RoutingExtensionTest extends TestCase
{
    public function testAnActivePathWithoutContextIsATranslatableError(): void
    {
        $router = $this->createStub(RouterInterface::class);
        $router->method('getContext')->willReturn(null);

        $exception = null;
        try {
            (new RoutingExtension($router))->isActivePath('home');
        } catch (Throwable $e) {
            $exception = $e;
        }

        $this->assertInstanceOf(LogicException::class, $exception);
        $this->assertInstanceOf(TranslatableInterface::class, $exception);
        $this->assertSame('Routing context not set.', $exception->getMessage());
    }
}
