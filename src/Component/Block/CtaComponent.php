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

#[AsTwigComponent('block-cta')]
class CtaComponent
{
    public string $title;

    public string $description;

    public string $button_text;

    public string $button_url;

    public int $text_cols = 8;

    public int $button_cols = 4;

    public string $theme = 'default';
}
