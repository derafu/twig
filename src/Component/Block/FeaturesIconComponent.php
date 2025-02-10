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

#[AsTwigComponent('block-features-icon')]
class FeaturesIconComponent
{
    // Features
    public array $features = [];    // Array de:
                                    // - icon: ícono de Font Awesome (ej: "fa-solid fa-cloud")
                                    // - title: título de la característica
                                    // - description: descripción con HTML permitido

    // Theme
    public string $theme = 'default';
}
