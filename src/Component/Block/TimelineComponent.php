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

#[AsTwigComponent('block-timeline')]
class TimelineComponent extends AbstractComponent
{
    /**
     * Array of timeline events configurations.
     *
     * Each event contains:
     * - date: Event date (YYYY-MM-DD, YYYYMM, YYYY)
     * - title: Event title (optional)
     * - description: Event description (required)
     * - icon: Font Awesome icon class (e.g. "fa-solid fa-check-circle")
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $events = [];

    /**
     * Position of the timeline line (center, left, right).
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $line_position = 'center';

    /**
     * Optional date format for event dates.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $date_format = null;
}
