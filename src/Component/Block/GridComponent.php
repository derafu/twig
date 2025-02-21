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

#[AsTwigComponent('block-grid')]
class GridComponent
{
   /**
    * Unique identifier for the grid component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the grid component.
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
    * Array of items to display in the grid.
    *
    * @var array
    */
   public array $items;

   /**
    * Number of columns in the grid layout.
    *
    * @var int
    */
   public int $cols;

   /**
    * Constructor for the Grid component.
    * Automatically generates a unique ID with 'grid-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('grid-');
   }
}
