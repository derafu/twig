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

#[AsTwigComponent('block-cta')]
class CtaComponent
{
   /**
    * Unique identifier for the CTA component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the CTA component.
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
    * Main title of the CTA.
    *
    * @var string
    */
   public string $title;

   /**
    * Description text of the CTA.
    *
    * @var string
    */
   public string $description;

   /**
    * Text to display on the CTA button.
    *
    * @var string
    */
   public string $button_text;

   /**
    * URL for the CTA button.
    *
    * @var string
    */
   public string $button_url;

   /**
    * Number of columns for the text section (Bootstrap grid).
    *
    * @var int
    */
   public int $text_cols = 8;

   /**
    * Number of columns for the button section (Bootstrap grid).
    *
    * @var int
    */
   public int $button_cols = 4;

   /**
    * Constructor for the CTA component.
    * Automatically generates a unique ID with 'cta-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('cta-');
   }
}
