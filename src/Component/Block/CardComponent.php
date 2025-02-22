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

#[AsTwigComponent('block-card')]
class CardComponent extends AbstractComponent
{
    /**
     * Optional card image URL.
     */
    #[ExposeInTemplate()]
    public ?string $image = null;

    /**
     * Optional card title.
     */
    #[ExposeInTemplate()]
    public ?string $title = null;

    /**
     * Optional card description.
     */
    #[ExposeInTemplate()]
    public ?string $description = null;

    /**
     * Optional button text.
     */
    #[ExposeInTemplate()]
    public ?string $buttonText = null;

    /**
     * Optional button URL (required if buttonText is present).
     */
    #[ExposeInTemplate()]
    public ?string $buttonUrl = null;
}
