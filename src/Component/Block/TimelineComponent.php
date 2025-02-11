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

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('block-timeline')]
class TimelineComponent
{
    // Layout
    public string $line_position = 'center';  // center, left, right

    /**
     * Content
     *
     * Array de:
     * - date: Fecha (YYYY-MM-DD, YYYYMM, YYYY)
     * - title: título (opcional)
     * - description: descripción (obligatorio)
     * - icon: ícono de Font Awesome (ej: "fa-solid fa-check-circle")
     *
     * @var array
     */
    public array $events = [];

    // Date format
    public ?string $date_format = null;  // Formato de fecha (opcional)

    // Theme
    public string $theme = 'default';
}
