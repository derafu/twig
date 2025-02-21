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

#[AsTwigComponent('block-features-icon')]
class FeaturesIconComponent
{
   /**
    * Unique identifier for the features icon component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the features icon component.
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
    * Array of features with their configurations.
    *
    * Each feature contains:
    * - icon: Font Awesome icon class (e.g. "fa-solid fa-display")
    * - title: Feature title
    * - description: Feature description (supports HTML)
    *
    * @var array
    */
   public array $features = [];

   /**
    * Constructor for the Features Icon component.
    * Automatically generates a unique ID with 'features-icon-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('features-icon-');
   }
}
