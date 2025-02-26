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

/**
 * Video Grid Component for displaying multiple videos in a grid layout.
 *
 * This component creates a responsive grid of video blocks with optional
 * titles, descriptions, and action buttons.
 */
#[AsTwigComponent('block-video-grid')]
class VideoGridComponent extends AbstractComponent
{
    /**
     * Array of videos with their configurations.
     *
     * Each video contains:
     *
     *   - title: Video title (optional).
     *   - video: YouTube URL (required).
     *   - description: Video description (optional, supports HTML).
     *   - buttons: Array of action buttons (optional).
     *      - text: Button text.
     *      - url: Button URL.
     *   - card: Display as card (optional, default: true).
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $videos = [];

    /**
     * Number of columns in the grid layout.
     *
     * @var int
     */
    #[ExposeInTemplate()]
    public int $cols = 2;
}
