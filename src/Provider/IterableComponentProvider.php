<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Provider;

use Derafu\Twig\Contract\ComponentProviderInterface;

/**
 * Adapts component classes or tagged component instances to a provider.
 */
final class IterableComponentProvider implements ComponentProviderInterface
{
    /**
     * @param iterable<class-string|object> $components
     */
    public function __construct(private readonly iterable $components)
    {
    }

    public function getComponents(): array
    {
        $classes = [];
        foreach ($this->components as $component) {
            $classes[] = is_object($component) ? $component::class : $component;
        }

        return array_values(array_unique($classes));
    }
}
