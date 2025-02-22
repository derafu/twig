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

#[AsTwigComponent('block-features-icon')]
class FeaturesIconComponent extends AbstractComponent
{
    /**
     * Array of features with their configurations.
     *
     * Each feature contains:
     * - icon: Font Awesome icon class (e.g. "fa-solid fa-display")
     * - title: Feature title
     * - description: Feature description (supports HTML)
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $features = [];

    protected ?string $container = 'container';
}
