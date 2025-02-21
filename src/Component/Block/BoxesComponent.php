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

#[AsTwigComponent('block-boxes')]
class BoxesComponent
{
   /**
    * Unique identifier for the boxes component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the boxes component.
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
    * Array of boxes with their configurations.
    *
    * Each box contains:
    * - icon: Icon name
    * - title: Box title
    * - description: Box description
    * - button_text: Button text
    * - button_url: Button URL
    *
    * @var array<int,array<string,string|null>>
    */
   public array $boxes = [];

   /**
    * Number of columns in the layout (2 or 3).
    *
    * @var int
    */
   public int $cols = 2;

   /**
    * Alignment of the boxes (left, center, right).
    *
    * @var string
    */
   public string $align = 'center';

   /**
    * Constructor for the Boxes component.
    * Automatically generates a unique ID with 'boxes-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('boxes-');
   }
}