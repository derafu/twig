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
 * Video Component for displaying embedded videos with additional content.
 *
 * This component creates customizable video blocks that can include
 * title, description, action buttons and various display options.
 */
#[AsTwigComponent('block-video')]
class VideoComponent extends AbstractComponent
{
    /**
     * Configuration array for video content.
     *
     * Structure:
     * - title: Video title (required)
     * - video: Video URL (required, supports YouTube URLs)
     * - description: Video description (optional, supports HTML)
     * - buttons: Array of action buttons (optional)
     *   - text: Button text
     *   - url: Button URL
     * - size: Video size (normal, small, default: normal)
     * - align: Video alignment (left, center, right, default: center)
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $video = [];

    /**
     * Unique identifier for the text-image component.
     */
    #[ExposeInTemplate()]
    public string $id;

    /**
     * Additional CSS classes for the text-image component.
     */
    #[ExposeInTemplate()]
    public ?string $class = null;

    /**
     * Container wrapper class (e.g., 'container' or 'container-fluid').
     */
    #[ExposeInTemplate()]
    public ?string $container = null;
}
