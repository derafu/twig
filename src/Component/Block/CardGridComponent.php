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

#[AsTwigComponent('block-card-grid')]
class CardGridComponent
{
    // Layout
    public int $cols = 4;    // 1, 2, 3, 4, o 6 columnas

    // Cards
    public array $cards = [];   // Array de:
                                // - image: URL de la imagen (opcional)
                                // - title: título (opcional)
                                // - description: descripción (opcional)
                                // - button_text: texto del botón (opcional)
                                // - button_url: URL del botón

    // Theme
    public string $theme = 'default';
}
