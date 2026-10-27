<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Contract;

use Twig\Extension\ExtensionInterface;

interface ExtensionProviderInterface
{
    /**
     * Extensions for an environment with the given configuration.
     *
     * @param array{extra?: bool, ...} $options
     * @return iterable<ExtensionInterface>
     */
    public function getExtensions(array $options = []): iterable;
}
