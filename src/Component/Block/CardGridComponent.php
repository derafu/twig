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

#[AsTwigComponent('block-card-grid')]
class CardGridComponent extends AbstractComponent
{
    /**
     * Array of cards with their configurations.
     *
     * Each card contains:
     * - image: Image URL (optional)
     * - title: Card title (optional)
     * - description: Card description (optional)
     * - button_text: Button text (optional)
     * - button_url: Button URL
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $cards = [];

    /**
     * Number of columns in the grid layout (1, 2, 3, 4, or 6).
     *
     * @var int
     */
    #[ExposeInTemplate()]
    public int $cols = 4;

    public ?int $offset = null;
}
