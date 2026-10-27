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

interface TemplatePathProviderInterface
{
    /**
     * Template directories in search order, highest priority first.
     *
     * @return list<string>
     */
    public function getPaths(): array;
}
