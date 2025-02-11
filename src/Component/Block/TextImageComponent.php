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

#[AsTwigComponent('block-text-image')]
class TextImageComponent
{
    // Layout

    public string $image_position = 'right';  // right, left

    public int $text_cols = 7;

    public int $image_cols = 5;

    // Contenido

    public string $title;

    public string $content;          // Texto que puede tener párrafos

    public array $buttons = [];      // Array de {text, url}

    public string $image;            // URL de la imagen

    // Visual
    public string $theme = 'default';
}
