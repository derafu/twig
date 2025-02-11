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

#[AsTwigComponent('block-footer')]
class FooterComponent
{
    // Columna 1

    public ?string $col1Title = null;

    public ?string $col1Html = null;

    public array $col1Links = [];

    public array $col1SocialIcons = [];

    // Columna 2

    public ?string $col2Title = null;

    public ?string $col2Html = null;

    public array $col2Links = [];

    public array $col2SocialIcons = [];

    // Columna 3

    public ?string $col3Title = null;

    public ?string $col3Html = null;

    public array $col3Links = [];

    public array $col3SocialIcons = [];

    // Columna 4

    public ?string $col4Title = null;

    public ?string $col4Html = null;

    public array $col4Links = [];

    public array $col4SocialIcons = [];

    // Footer bottom

    public ?string $leftText = null;

    public ?string $rightText = null;

    // Configuración general

    public bool $socialIconsCircular = false;

    public string $theme = 'default';
}
