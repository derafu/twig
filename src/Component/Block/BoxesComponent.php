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

#[AsTwigComponent('block-boxes')]
class BoxesComponent extends AbstractComponent
{
    /**
     * Array of boxes with their configurations.
     *
     * Each box contains:
     * - icon: Icon name
     * - title: Box title
     * - description: Box description
     * - button_text: Button text
     * - button_url: Button URL
     *
     * @var array<int,array<string,string|null>>
     */
    #[ExposeInTemplate()]
    public array $boxes = [];

    /**
     * Number of columns in the layout (2 or 3).
     *
     * @var int
     */
    #[ExposeInTemplate()]
    public int $cols = 2;

    /**
     * Alignment of the boxes (left, center, right).
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $align = 'center';
}
