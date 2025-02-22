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

#[AsTwigComponent('block-offcanvas')]
class OffcanvasComponent extends AbstractComponent
{
    /**
     * Position of the offcanvas (start, end, top, bottom).
     * Default: start (left side).
     */
    #[ExposeInTemplate()]
    public string $position = 'start';

    /**
     * Offcanvas title (optional).
     */
    #[ExposeInTemplate()]
    public ?string $title = null;

    /**
     * Offcanvas content (supports HTML).
     */
    #[ExposeInTemplate()]
    public string $content;

    /**
     * Show close button in header.
     */
    #[ExposeInTemplate()]
    public bool $showClose = true;

    /**
     * Enable backdrop.
     */
    #[ExposeInTemplate()]
    public bool $backdrop = true;

    /**
     * Enable body scrolling while offcanvas is visible.
     */
    #[ExposeInTemplate()]
    public bool $scroll = false;

    /**
     * Array of footer buttons.
     *
     * Structure: [
     *   'text' => string,
     *   'type' => string (primary, secondary, etc),
     *   'dismiss' => bool (optional),
     *   'attributes' => string (optional, additional HTML attributes)
     * ]
     */
    #[ExposeInTemplate()]
    public array $buttons = [];
}
