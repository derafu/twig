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

use Derafu\Twig\Contract\TemplatePathProviderInterface;

/**
 * Combines domain-owned directories in provider order, keeping the first path.
 */
final class ChainTemplatePathProvider implements TemplatePathProviderInterface
{
    /**
     * @param iterable<TemplatePathProviderInterface> $providers Highest priority first.
     */
    public function __construct(private readonly iterable $providers = [])
    {
    }

    public function getPaths(): array
    {
        $paths = [];
        foreach ($this->providers as $provider) {
            $paths = [...$paths, ...$provider->getPaths()];
        }

        return array_values(array_unique($paths));
    }
}
