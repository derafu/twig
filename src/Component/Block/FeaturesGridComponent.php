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

#[AsTwigComponent('block-features-grid')]
class FeaturesGridComponent
{
    /**
     * Content blocks
     *
     * Array de:
     *
     *   - title: título del bloque
     *   - subtitle: subtítulo del bloque
     *   - features: array de características
     *     - icon: ícono de Font Awesome
     *     - title: título de la característica
     *     - description: descripción (permite HTML)
     *
     * @var array
     */
    public array $blocks = [];

    // Theme
    public string $theme = 'default';
}
