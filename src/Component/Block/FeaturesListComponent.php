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

#[AsTwigComponent('block-features-list')]
class FeaturesListComponent extends AbstractComponent
{
        /**
     * Array of features for the left side.
     *
     * Each feature contains:
     * - icon: Font Awesome icon class (e.g. "fa-solid fa-display")
     * - title: Feature title
     * - description: Feature description (supports HTML)
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $features_left = [];

    /**
     * Array of features for the right side.
     *
     * Each feature contains:
     * - icon: Font Awesome icon class (e.g. "fa-solid fa-display")
     * - title: Feature title
     * - description: Feature description (supports HTML)
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $features_right = [];

    /**
     * Unique identifier for the features list component.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $id = '';

    /**
     * Container class for the features list component.
     * If null, defaults to 'container' when enabled.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $container = null;

    /**
     * Additional CSS classes for the features list component.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $class = null;

}
