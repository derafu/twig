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

#[AsTwigComponent('block-photo')]
class PhotoComponent
{
    // Contenido

    public string $title;

    public string $image;            // URL de la imagen

    public ?string $description = null;

    public array $buttons = [];      // Array de {text, url}

    // Layout

    public string $align = 'center';  // Alineación del contenido

    public string $size = 'medium';   // small, medium, large

    // Theme
    public string $theme = 'default';
}
