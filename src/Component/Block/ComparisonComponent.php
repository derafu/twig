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

#[AsTwigComponent('block-comparison')]
class ComparisonComponent
{
   /**
    * Unique identifier for the comparison component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the comparison component.
    *
    * @var string
    */
   public string $theme = 'default';

   /**
    * Use container wrapper.
    *
    * @var string
    */
   public string $container = 'container';

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
   public array $plans = [];

   /**
    * Constructor for the Comparison component.
    * Automatically generates a unique ID with 'comparison-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('comparison-');
   }
}
