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
 * Title Component for displaying customizable headings.
 *
 * This component creates headings with optional subtitle and styling options
 * like alignment and bottom border.
 */
#[AsTwigComponent('block-title')]
class TitleComponent extends AbstractComponent
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
     * The title text (supports HTML)
     */
    #[ExposeInTemplate()]
    public string $title;

    /**
     * Optional subtitle text (supports HTML)
     */
    #[ExposeInTemplate()]
    public ?string $subtitle = null;

    /**
     * Heading level (1-6 => h1-h6)
     */
    #[ExposeInTemplate()]
    public int $level = 2;

    /**
     * Text alignment (left, center, right)
     */
    #[ExposeInTemplate()]
    public string $align = 'center';

    /**
     * Show bottom border
     */
    #[ExposeInTemplate()]
    public bool $bottomBorder = true;
}
