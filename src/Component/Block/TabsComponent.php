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

#[AsTwigComponent('block-tabs')]
class TabsComponent
{
    // Tabs configuration
    public array $tabs = [];
    public ?string $activeTab = null; // if null, first tab will be active

    // Display options
    public string $position = 'horizontal';  // 'horizontal' | 'vertical'
    public int $cols = 2;  // cols for vertical layout

    // Theme
    public string $theme = 'default';
}
