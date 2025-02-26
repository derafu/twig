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

#[AsTwigComponent('block-accordion')]
class AccordionComponent extends AbstractComponent
{
    /**
     * Array of accordion items.
     *
     * Each item should contain:
     *
     *   - `title`.
     *   - `content`.
     *   - `active`.
     *
     * @var array<int,array>
     */
    #[ExposeInTemplate()]
    public array $items = [];
}
