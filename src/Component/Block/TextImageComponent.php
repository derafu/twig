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

#[AsTwigComponent('block-text-image')]
class TextImageComponent extends AbstractComponent
{
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

    /**
     * Position of the image (right, left).
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $image_position = 'right';

    /**
     * Number of columns for text section (Bootstrap grid).
     *
     * @var int
     */
    #[ExposeInTemplate()]
    public int $text_cols = 7;

    /**
     * Title of the text section.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $title;

    /**
     * Main content text (supports paragraphs).
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $content;

    /**
     * Array of button configurations.
     * Each button contains: {text, url}
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $buttons = [];

    /**
     * URL of the featured image.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $image;

    /**
     * Optional flag to indicate rendering inside tabs.
     *
     * @var bool
     */
    #[ExposeInTemplate()]
    public bool $inside_tabs = false;
}
