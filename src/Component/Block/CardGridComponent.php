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
     * Unique identifier for the card grid component.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $id = '';

    /**
     * Container class for the card grid component.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $container = null;

    /**
     * Additional CSS classes for the card grid component.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $class = null;

    /**
     * Number of columns in the grid layout (1, 2, 3, 4, or 6).
     *
     * @var int
     */
    #[ExposeInTemplate()]
    public int $cols = 4;

    /**
     * Optional offset for the grid columns.
     *
     * @var int|null
     */
    #[ExposeInTemplate()]
    public ?int $offset = null;
}
