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

use Derafu\Twig\Abstract\AbstractComponent;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent('block-image-grid')]
class ImageGridComponent extends AbstractComponent
{
    /**
     * Array of images with their configurations.
     *
     * Each image contains:
     * - image: Image URL
     * - url: Link URL (optional)
     * - tooltip: Hover text (optional)
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $images = [];

    /**
     * Number of columns in the grid layout (4 or 6).
     *
     * @var int
     */
    #[ExposeInTemplate()]
    public int $cols = 6;
}
