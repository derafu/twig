<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Contract;

use Twig\Environment;
use Twig\Extension\ExtensionInterface;

interface TwigCreatorInterface
{
    /**
     * Creates a Twig environment.
     *
     * @param array{paths?: string|array<string>, extensions?: iterable<ExtensionInterface>, components?: ComponentProviderInterface|iterable<class-string|object>, extra?: bool, ...} $options
     * Options of the environment.
     * @return Environment
     */
    public function create(array $options = []): Environment;
}
