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
 * Quote Component for displaying testimonials, citations, or quotes.
 *
 * This component creates a styled quote block that can include the quote text,
 * author information, source reference, and an optional author image.
 */
#[AsTwigComponent('block-quote')]
class QuoteComponent extends AbstractComponent
{
    /**
     * The quote text.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $quote = null;

    /**
     * The quote author name.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $author = null;

    /**
     * The quote source or reference.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $source = null;

    /**
     * The author image URL.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $image = null;

    protected ?string $container = 'container';
}
