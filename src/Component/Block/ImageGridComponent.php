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

#[AsTwigComponent('block-image-grid')]
class ImageGridComponent
{
   /**
    * Unique identifier for the image grid component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the image grid component.
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
    * Number of columns in the grid layout (4 or 6).
    *
    * @var int
    */
   public int $cols = 6;

   /**
    * Array of images with their configurations.
    *
    * Each image contains:
    * - image: Image URL
    * - url: Link URL (optional)
    * - tooltip: Hover text (optional)
    *
    * @var array
    */
   public array $images = [];

   /**
    * Constructor for the Image Grid component.
    * Automatically generates a unique ID with 'image-grid-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('image-grid-');
   }
}
