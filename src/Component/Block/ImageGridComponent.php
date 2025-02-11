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

#[AsTwigComponent('block-image-grid')]
class ImageGridComponent
{
    // Layout
    public int $cols = 6;    // 4 o 6 columnas

    /**
     * Imágenes
     *
     * Array de:
     *   - image: URL de la imagen
     *   - url: enlace (opcional)
     *   - tooltip: texto al hover (opcional)
     *
     * @var array
     */
    public array $images = [];

    // Theme
    public string $theme = 'default';
}
