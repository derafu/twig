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

#[AsTwigComponent('block-hero', exposePublicProps: false)]
class HeroComponent extends AbstractComponent
{
    /**
     * Unique identifier for the hero component.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $id = '';

    /**
     * Container class for the hero component.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $container = null;

    /**
     * Additional CSS classes for the hero component.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $class = null;

    /**
     * Size of the hero section (mini, small, medium, large, full).
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $size = 'medium';

    /**
     * Content alignment (left, center, right).
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $align = 'center';

    /**
     * Background image URL or color.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $background = null;

    /**
     * Main title of the hero section.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $title;

    /**
     * Subtitle text of the hero section.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $subtitle = null;

    /**
     * Array of buttons configurations.
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $buttons = [];

    /**
     * Background color for content wrapper.
     * Example: 'rgba(255, 255, 255, 0.9)' or '#ffffff'.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $contentBackground = null;
}
