<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Contract;

use Twig\Environment;

interface TwigServiceInterface
{
    /**
     * Renders a Twig template into HTML.
     *
     * @param string $template Twig template to render.
     * @param array $data Data to be passed to the Twig template.
     * @return string HTML code with the rendered Twig template.
     */
    public function render(string $template, array &$data = []): string;

    /**
     * Returns the Twig instance.
     *
     * This method avoids creating it in the constructor and only creates it
     * when it is actually used. Useful when using lazy services.
     *
     * @return Environment
     */
    public function getTwig(): Environment;
}
