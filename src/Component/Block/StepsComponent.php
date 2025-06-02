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

#[AsTwigComponent('block-steps')]
class StepsComponent extends AbstractComponent
{
    /**
     * Unique identifier for the steps component.
     */
    #[ExposeInTemplate()]
    public string $id;

    /**
     * Additional CSS classes for the steps component.
     */
    #[ExposeInTemplate()]
    public ?string $class = null;

    /**
     * Container wrapper class (e.g., 'container' or 'container-fluid').
     */
    #[ExposeInTemplate()]
    public ?string $container = null;

    /**
     * Array of steps with their configurations.
     *
     * Each step contains:
     * - icon: Font Awesome icon class (e.g. "fa-solid fa-list")
     * - title: Step title
     * - description: Step description (supports HTML)
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $steps = [];

    /**
     * Style of arrows connecting steps (curved, straight).
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $arrow_type = 'curved';
}
