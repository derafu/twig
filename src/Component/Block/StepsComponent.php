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

#[AsTwigComponent('block-steps')]
class StepsComponent
{
    // Theme
    public string $theme = 'default';

    /**
     * Steps
     *
     * Array de:
     *
     *   - icon: ícono de Font Awesome (ej: "fa-solid fa-list")
     *   - title: título del paso
     *   - description: descripción con HTML permitido
     *
     * @var array
     */
    public array $steps = [];

    // Arrow type
    public string $arrow_type = 'curved';  // curved, straight
}
