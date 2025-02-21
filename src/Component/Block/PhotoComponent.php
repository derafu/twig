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

#[AsTwigComponent('block-photo')]
class PhotoComponent
{
   /**
    * Unique identifier for the photo component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the photo component.
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
    * Title of the photo section.
    *
    * @var string
    */
   public string $title;

   /**
    * URL of the photo image.
    *
    * @var string
    */
   public string $image;

   /**
    * Optional description text for the photo.
    *
    * @var string|null
    */
   public ?string $description = null;

   /**
    * Array of buttons configurations.
    * Each button contains: {text, url}
    *
    * @var array
    */
   public array $buttons = [];

   /**
    * Content alignment (center, left, right).
    *
    * @var string
    */
   public string $align = 'center';

   /**
    * Size of the photo section (small, medium, large).
    *
    * @var string
    */
   public string $size = 'medium';

   /**
    * Constructor for the Photo component.
    * Automatically generates a unique ID with 'photo-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('photo-');
   }
}
