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

#[AsTwigComponent('block-cta')]
class CtaComponent extends AbstractComponent
{
    /**
     * Container class for the CTA component.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $container = null;

    /**
     * Unique identifier for the CTA component.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $id = '';

    /**
     * Additional CSS classes for the CTA component.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $class = null;

    /**
     * Main title of the CTA.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $title;

    /**
     * Description text of the CTA.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $description;

    /**
     * Text to display on the CTA button.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $button_text;

    /**
     * URL for the CTA button.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $button_url;

    /**
     * Number of columns for the text section (Bootstrap grid).
     *
     * @var int
     */
    #[ExposeInTemplate()]
    public int $text_cols = 8;

    /**
     * Number of columns for the button section (Bootstrap grid).
     *
     * @var int
     */
    #[ExposeInTemplate()]
    public int $button_cols = 4;
}
