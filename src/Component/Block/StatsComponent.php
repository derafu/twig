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

#[AsTwigComponent('block-stats')]
class StatsComponent
{
    // Configuración
    public string $text_position = 'bottom';  // bottom, top
    public array $stats = [];  // Array de {number, text}

    // Visual
    public string $theme = 'default';
}
