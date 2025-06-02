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

#[AsTwigComponent('block-comparison')]
class ComparisonComponent extends AbstractComponent
{
        /**
     * Array of plans with their configurations.
     *
     * Each plan contains:
     * - title: Plan title
     * - price: Plan price
     * - period: Billing period (/month, /year, etc)
     * - features: Array of features:
     *   - text: Feature text
     *   - value: Specific value or true/false
     *   - tooltip: Optional tooltip
     * - button_text: Button text
     * - button_url: Button URL
     * - highlighted: true/false to highlight the plan
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $plans = [];

    /**
     * Unique identifier for the comparison component.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $id = '';

    /**
     * Container class for the comparison component.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $container = null;

    /**
     * Additional CSS classes for the comparison component.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $class = null;

}
