<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Component\Block;

use Derafu\Twig\Abstract\AbstractComponent;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Gallery Component for displaying images and videos in a grid with a
 * click-to-enlarge lightbox.
 *
 * This component creates a responsive grid of thumbnails (images and/or
 * videos) that open in a single lightbox (Bootstrap modal + carousel) when
 * clicked, allowing navigation between items with the mouse (carousel
 * controls) or the keyboard (arrow keys).
 */
#[AsTwigComponent('block-gallery')]
class GalleryComponent extends AbstractComponent
{
    /**
     * Array of gallery items.
     *
     * Each item contains:
     *
     *   - image: Full size image URL (required if the item is an image).
     *   - video: YouTube URL (required if the item is a video).
     *   - thumbnail: Thumbnail URL shown in the grid (optional). Defaults to
     *     `image` for image items. For video items without a thumbnail, a
     *     play icon placeholder is shown instead.
     *   - caption: Caption shown below the thumbnail and in the lightbox
     *     (optional).
     *   - alt: Accessible text / tooltip for the item (optional).
     *
     * @var array
     */
    private array $items = [];

    /**
     * Number of columns in the thumbnails grid (2, 3, 4 or 6).
     *
     * @var int
     */
    private int $cols = 4;

    /**
     * Lightbox modal size (sm, lg, xl, fullscreen).
     *
     * @var string
     */
    private string $size = 'xl';

    /**
     * Gets the array of gallery items.
     *
     * @return array The array of gallery items.
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Sets the array of gallery items.
     *
     * @param array $items The array of gallery items.
     * @return static
     */
    public function setItems(array $items): static
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Gets the number of columns in the thumbnails grid.
     *
     * @return int The number of columns.
     */
    public function getCols(): int
    {
        return $this->cols;
    }

    /**
     * Sets the number of columns in the thumbnails grid.
     *
     * @param int $cols The number of columns.
     * @return static
     */
    public function setCols(int $cols): static
    {
        $this->cols = $cols;

        return $this;
    }

    /**
     * Gets the lightbox modal size.
     *
     * @return string The lightbox modal size.
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * Sets the lightbox modal size.
     *
     * @param string $size The lightbox modal size.
     * @return static
     */
    public function setSize(string $size): static
    {
        $this->size = $size;

        return $this;
    }
}
