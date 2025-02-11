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
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('block-header')]
class HeaderComponent
{
    // Logo configuration

    public ?string $logoImage = null;

    public ?string $logoHtml = null;

    public int $logoMaxWidth = 150;

    // Navigation items

    public array $leftNav = [];

    public array $rightNav = [];

    // Call to action button

    public ?string $ctaIcon = null;

    public ?string $ctaText = null;

    public ?string $ctaUrl = null;

    // Dropdown behavior
    public string $dropdownTrigger = 'click';

    // Position
    public string $position = 'normal';

    // Background color (optional)
    public ?string $backgroundColor = null;

    // Theme
    public string $theme = 'default';

    #[PreMount]
    public function preMount(array $data): array
    {
        // Process left navigation
        if (isset($data['leftNav'])) {
            $data['leftNav'] = array_map([$this, 'processNavItem'], $data['leftNav']);
        }

        // Process right navigation
        if (isset($data['rightNav'])) {
            $data['rightNav'] = array_map([$this, 'processNavItem'], $data['rightNav']);
        }

        return $data;
    }

    private function processNavItem(array $item): array
    {
        // Detect item type based on its structure
        if (isset($item['sections'])) {
            $item['type'] = 'mega-dropdown';
        } elseif (isset($item['items'])) {
            $item['type'] = 'dropdown';
            // Process subitems recursively
            $item['items'] = array_map(function ($subitem) {
                if (isset($subitem['items'])) {
                    $subitem['type'] = 'submenu';
                } elseif (!isset($subitem['type'])) {
                    $subitem['type'] = 'link';
                }
                return $subitem;
            }, $item['items']);
        } else {
            $item['type'] = 'link';
        }

        return $item;
    }
}
