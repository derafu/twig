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

#[AsTwigComponent('block-testimonials')]
class TestimonialsComponent extends AbstractComponent
{
    /**
     * Recommended width for background images.
     */
    public const RECOMMENDED_IMAGE_WIDTH = 1920;

    /**
     * Recommended height for background images.
     */
    public const RECOMMENDED_IMAGE_HEIGHT = 500;

    /**
     * Array of testimonials configurations.
     *
     * Each testimonial contains:
     * - image: Background image URL
     * - quote: Testimonial text
     * - author: Author name
     * - company: Company name (optional)
     * - url: Link URL (optional)
     * - position: Card position (center, left, right)
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $testimonials = [];

    /**
     * Enable automatic slideshow.
     *
     * @var bool
     */
    #[ExposeInTemplate()]
    public bool $autoplay = true;

    /**
     * Time between slides in milliseconds.
     *
     * @var int
     */
    #[ExposeInTemplate()]
    public int $interval = 5000;

    /**
     * Show navigation controls.
     *
     * @var bool
     */
    #[ExposeInTemplate()]
    public bool $showControls = true;

    /**
     * Show slide indicators.
     *
     * @var bool
     */
    #[ExposeInTemplate()]
    public bool $showIndicators = true;

    /**
     * Maximum width of testimonial cards in pixels.
     *
     * @var int
     */
    #[ExposeInTemplate()]
    public int $maxWidth = 600;

    /**
     * Background image brightness percentage.
     *
     * @var int
     */
    #[ExposeInTemplate()]
    public int $brightness = 70;

    /**
     * Height of testimonial section in pixels.
     *
     * @var int
     */
    #[ExposeInTemplate()]
    public int $height = 500;
}
