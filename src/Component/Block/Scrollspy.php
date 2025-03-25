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

use Derafu\Twig\Abstract\AbstractComponent;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

/**
 * Scrollspy Bootstrap Component
 *
 * Allows creating a navigation by anchor points with smooth scrolling.
 */
#[AsTwigComponent('block-scrollspy')]
class ScrollspyComponent extends AbstractComponent
{
    /**
     * Orientation of the scrollspy.
     * Values: 'vertical', 'horizontal'.
     */
    #[ExposeInTemplate()]
    public string $orientation = 'vertical';

    /**
     * Height of the scrollspy.
     * Values: '100vh', '100%', 'auto'.
     */
    #[ExposeInTemplate()]
    public string $height = '100vh';

    /**
     * Array of scrollspy sections.
     *
     * Each section should contain:
     *   - `id`: Unique identifier for the section
     *   - `title`: Navigation item text
     *   - `content`: Section content
     *   - `icon`: (Optional) FontAwesome icon class
     *   - `badge`: (Optional) Badge text
     *
     * @var array<int,array>
     */
    #[ExposeInTemplate()]
    public array $sections = [];

    /**
     * Number of columns for the navigation list (1-11).
     * Content will automatically use the remaining columns.
     */
    #[ExposeInTemplate()]
    public int $cols = 4;

    /**
     * Custom classes to add to the component.
     */
    #[ExposeInTemplate()]
    public ?string $class = null;

    /**
     * Custom inline styles to add to the component.
     */
    #[ExposeInTemplate()]
    public ?string $style = null;

    /**
     * Validate and prepare sections before rendering.
     *
     * @return void
     */
    public function mount(): void
    {
        // Provide default structure if no sections are passed.
        if (empty($this->sections)) {
            $this->sections = [
                [
                    'id' => 'default-section',
                    'title' => 'Default Section',
                    'content' => 'Default content for scrollspy section.',
                    'icon' => null,
                    'badge' => null,
                ],
            ];
        }

        // Process and validate all sections.
        $this->sections = array_map(function ($section) {
            // Generate ID if not provided.
            if (empty($section['id'])) {
                $section['id'] = 'section-' . uniqid();
            }

            // Ensure each section has all required keys.
            return [
                'id' => $section['id'],
                'title' => $section['title'] ?? 'Untitled',
                'content' => $section['content'] ?? '',
                'icon' => $section['icon'] ?? null,
                'badge' => $section['badge'] ?? null,
            ];
        }, $this->sections);

        // Validate orientation.
        $validOrientations = ['vertical', 'horizontal'];
        $this->orientation = in_array($this->orientation, $validOrientations)
            ? $this->orientation
            : 'vertical';

        // Validate cols (between 1 and 11).
        $this->cols = max(1, min(11, (int) $this->cols));
    }
}