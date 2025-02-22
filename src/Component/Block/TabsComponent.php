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

#[AsTwigComponent('block-tabs')]
class TabsComponent extends AbstractComponent
{
    /**
     * Array of tab configurations.
     *
     * Each tab contains title and content.
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $tabs = [];

    /**
     * Identifier of the active tab. First tab is active if null.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $activeTab = null;

    /**
     * Layout orientation of tabs (horizontal, vertical).
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $position = 'horizontal';

    /**
     * Number of columns for vertical layout.
     *
     * @var int
     */
    #[ExposeInTemplate()]
    public int $cols = 2;

    protected ?string $container = 'container';
}
