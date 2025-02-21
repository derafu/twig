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

#[AsTwigComponent('block-hero', exposePublicProps: false)]
class HeroComponent
{
   /**
    * Unique identifier for the hero component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the hero component.
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
    * Size of the hero section (small, medium, large).
    *
    * @var string
    */
   public string $size = 'medium';

   /**
    * Content alignment (left, center, right).
    *
    * @var string
    */
   public string $align = 'center';

   /**
    * Background image URL or color.
    *
    * @var string
    */
   public string $background;

   /**
    * Main title of the hero section.
    *
    * @var string
    */
   public string $title;

   /**
    * Subtitle text of the hero section.
    *
    * @var string
    */
   public string $subtitle;

   /**
    * Array of buttons configurations.
    *
    * @var array
    */
   public array $buttons = [];

   /**
    * Constructor for the Hero component.
    * Automatically generates a unique ID with 'alert-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('alert-');
   }
}