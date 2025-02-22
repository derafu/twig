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

#[AsTwigComponent('block-stats')]
class StatsComponent extends AbstractComponent
{
    /**
     * Array of statistics configurations.
     * Each stat contains: {number, text}
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $stats = [];

    /**
     * Position of the text relative to numbers (bottom, top).
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $text_position = 'bottom';
}
