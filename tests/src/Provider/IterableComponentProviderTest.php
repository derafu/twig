<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Provider;

use ArrayIterator;
use Derafu\TestsTwig\Fixture\Component\AlertComponent;
use Derafu\TestsTwig\Fixture\Component\GreetingComponent;
use Derafu\Twig\Provider\IterableComponentProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IterableComponentProvider::class)]
final class IterableComponentProviderTest extends TestCase
{
    public function testClassesAndInstancesAreCollectedWithoutRepeatingClasses(): void
    {
        $provider = new IterableComponentProvider(new ArrayIterator([
            AlertComponent::class,
            new GreetingComponent(),
            new AlertComponent(),
        ]));
        self::assertSame([AlertComponent::class, GreetingComponent::class], $provider->getComponents());
    }

    public function testAnEmptyCollectionHasNoComponents(): void
    {
        self::assertSame([], (new IterableComponentProvider([]))->getComponents());
    }
}
