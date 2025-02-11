<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.org>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Component\Block;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('block-boxes')]
class BoxesComponent
{
    /**
     * Boxes - array de cajas.
     *
     * Cada una con:
     *
     *   - icon: nombre del ícono
     *   - title: título de la caja
     *   - description: descripción
     *   - button_text: texto del botón
     *   - button_url: URL del botón
     *
     * @var array
     */
    public array $boxes = [];

    // Layout

    public int $cols = 2;  // 2 o 3 columnas

    public string $align = 'center';    // left, center, right

    // Theme
    public string $theme = 'default';
}
