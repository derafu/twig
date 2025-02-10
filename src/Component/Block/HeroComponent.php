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

#[AsTwigComponent('block-hero', exposePublicProps: false)]
class HeroComponent
{
    // Configuración general.
    public string $size = 'medium'; // small, medium, large
    public string $align = 'center'; // left, center, right
    public string $background;

    // Contenido (todo opcional).
    public string $title;
    public string $subtitle;
    public array $buttons = [];

    // Theme.
    public string $theme = 'default';
}
