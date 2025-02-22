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

#[AsTwigComponent('block-photo')]
class PhotoComponent extends AbstractComponent
{
    /**
     * Title of the photo section.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $title;

    /**
     * URL of the photo image.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $image;

    /**
     * Optional description text for the photo.
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
     * Size of the photo section (small, medium, large).
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $size = 'medium';
}
