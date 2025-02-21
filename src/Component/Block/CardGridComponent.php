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

#[AsTwigComponent('block-card-grid')]
class CardGridComponent
{
   /**
    * Unique identifier for the card grid component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the card grid component.
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
    * Number of columns in the grid layout (1, 2, 3, 4, or 6).
    *
    * @var int
    */
   public int $cols = 4;

   /**
    * Array of cards with their configurations.
    *
    * Each card contains:
    * - image: Image URL (optional)
    * - title: Card title (optional)
    * - description: Card description (optional)
    * - button_text: Button text (optional)
    * - button_url: Button URL
    *
    * @var array
    */
   public array $cards = [];

   /**
    * Constructor for the Card Grid component.
    * Automatically generates a unique ID with 'card-grid-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('card-grid-');
   }
}
