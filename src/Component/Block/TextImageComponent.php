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

#[AsTwigComponent('block-text-image')]
class TextImageComponent
{
   /**
    * Unique identifier for the text-image component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the text-image component.
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
    * Position of the image (right, left).
    *
    * @var string
    */
   public string $image_position = 'right';

   /**
    * Number of columns for text section (Bootstrap grid).
    *
    * @var int
    */
   public int $text_cols = 7;

   /**
    * Number of columns for image section (Bootstrap grid).
    *
    * @var int
    */
   public int $image_cols = 5;

   /**
    * Title of the text section.
    *
    * @var string
    */
   public string $title;

   /**
    * Main content text (supports paragraphs).
    *
    * @var string
    */
   public string $content;

   /**
    * Array of button configurations.
    * Each button contains: {text, url}
    *
    * @var array
    */
   public array $buttons = [];

   /**
    * URL of the featured image.
    *
    * @var string
    */
   public string $image;

   /**
    * Constructor for the Text-Image component.
    * Automatically generates a unique ID with 'text-image-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('text-image-');
   }
}
