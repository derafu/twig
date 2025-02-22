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

#[AsTwigComponent('block-features-grid')]
class FeaturesGridComponent extends AbstractComponent
{
    /**
     * Array of content blocks with their configurations.
     *
     * Each block contains:
     * - title: Block title
     * - subtitle: Block subtitle
     * - features: Array of features:
     *   - icon: Font Awesome icon class
     *   - title: Feature title
     *   - description: Feature description (supports HTML)
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $blocks = [];

    protected ?string $container = 'container';
}
