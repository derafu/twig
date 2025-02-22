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

#[AsTwigComponent('block-grid')]
class GridComponent extends AbstractComponent
{
    /**
     * Array of items to display in the grid.
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $items;

    /**
     * Number of columns in the grid layout.
     *
     * @var int|null
     */
    #[ExposeInTemplate()]
    public ?int $cols = null;

    protected ?string $container = 'container';
}
