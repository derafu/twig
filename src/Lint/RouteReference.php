<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Lint;

/**
 * A reference to a route found in a template: a call to `path()`, `url()` or
 * `is_active_path()`.
 *
 * Part of the lint tools: it is for tools and tests, never for the code that
 * runs the package.
 */
final readonly class RouteReference
{
    /**
     * @param string $function Name of the Twig function that was called.
     * @param string|null $name Name of the route, or `null` when it is not a
     * literal and so it is only known when the template is rendered.
     * @param string $template Name of the template.
     * @param int $line Line of the call in the template.
     */
    public function __construct(
        public string $function,
        public ?string $name,
        public string $template,
        public int $line
    ) {
    }

    /**
     * Whether the name of the route is only known when the template is
     * rendered, so it can not be checked by reading it.
     */
    public function isDynamic(): bool
    {
        return $this->name === null;
    }
}
