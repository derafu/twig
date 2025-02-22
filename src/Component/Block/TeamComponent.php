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

#[AsTwigComponent('block-team')]
class TeamComponent extends AbstractComponent
{
    /**
     * Array of team members configurations.
     *
     * Each member contains:
     * - image: Member photo URL
     * - name: Member name
     * - role: Member role/position
     * - bio: Member biography (optional)
     * - links: Array of social/contact links:
     *   - icon: Link icon (optional)
     *   - text: Link text
     *   - url: Link URL
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $members = [];

    /**
     * Number of columns in the grid layout (1, 2, 3, 4, or 6).
     *
     * @var int
     */
    #[ExposeInTemplate()]
    public int $cols = 4;

    /**
     * Content alignment (left, center, right).
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $align = 'center';

    protected ?string $container = 'container';
}
