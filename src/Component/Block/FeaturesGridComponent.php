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

#[AsTwigComponent('block-features-grid')]
class FeaturesGridComponent
{
   /**
    * Unique identifier for the features grid component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the features grid component.
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
   public array $blocks = [];

   /**
    * Constructor for the Features Grid component.
    * Automatically generates a unique ID with 'features-grid-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('features-grid-');
   }
}
