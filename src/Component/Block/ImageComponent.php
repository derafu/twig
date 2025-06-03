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

#[AsTwigComponent('block-image')]
class ImageComponent extends AbstractComponent
{
    /**
     * Unique identifier for the image component.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $id = '';

    /**
     * Container class for the image component.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $container = null;

    /**
     * Additional CSS classes for the image component.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $class = null;

    /**
     * Title of the image section.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $title;

    /**
     * URL of the image.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $image;

    /**
     * Optional description text for the image.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $description = null;

    /**
     * Array of buttons configurations.
     * Each button contains: {text, url}
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $buttons = [];

    /**
     * Content alignment (center, left, right).
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $align = 'center';

    /**
     * Size of the image section (small, medium, large).
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $size = 'medium';
}
