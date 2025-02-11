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

#[AsTwigComponent('block-features-list')]
class FeaturesListComponent
{
    /**
     * Features left side
     *
     * Array de:
     *
     *   - icon: ícono de Font Awesome (ej: "fa-solid fa-display")
     *   - title: título
     *   - description: descripción (permite HTML)
     *
     * @var array
     */
    public array $features_left = [];

    /**
     * Features right side
     *
     * Array de:
     *
     *   - icon: ícono de Font Awesome (ej: "fa-solid fa-display")
     *   - title: título
     *   - description: descripción (permite HTML)
     *
     * @var array
     */
    public array $features_right = [];

    // Theme
    public string $theme = 'default';
}
