<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Provider;

use Derafu\TestsTwig\Fixture\Component\AlertComponent;
use Derafu\Twig\Provider\ArrayComponentProvider;
use Derafu\Twig\Provider\ChainComponentProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * The providers of components that do not read a directory.
 */
#[CoversClass(ArrayComponentProvider::class)]
#[CoversClass(ChainComponentProvider::class)]
final class ComponentProvidersTest extends TestCase
{
    public function testTheArrayProviderGivesTheComponentsItWasCreatedWith(): void
    {
        $provider = new ArrayComponentProvider([AlertComponent::class, stdClass::class]);

        $this->assertSame([AlertComponent::class, stdClass::class], $provider->getComponents());
    }

    public function testTheChainProviderGivesTheComponentsOfAllWithoutRepeatingThem(): void
    {
        $provider = new ChainComponentProvider(
            new ArrayComponentProvider([AlertComponent::class]),
            new ArrayComponentProvider([stdClass::class, AlertComponent::class])
        );

        $this->assertSame([AlertComponent::class, stdClass::class], array_values($provider->getComponents()));
    }

    public function testTheChainProviderWithoutProvidersHasNoComponents(): void
    {
        $this->assertSame([], (new ChainComponentProvider())->getComponents());
    }
}
