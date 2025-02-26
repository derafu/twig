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

#[AsTwigComponent('block-features-table')]
class FeaturesTableComponent extends AbstractComponent
{
    /**
     * Title of the features table
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $title = 'Features';

    /**
     * Features array containing the rows of the table
     *
     * Example structure:
     * [
     *     'icon' => 'fa-solid fa-folder',
     *     'name' => 'Feature Name',
     *     'description' => 'Optional description text',
     *     'values' => [
     *         'Plan A' => true,                   // For boolean (check/cross).
     *         'Plan B' => '1,400',                // For text/numeric.
     *         'Plan C' => '<strong>...</strong>'  // For HTML content.
     *     ]
     * ]
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $features = [];
}
