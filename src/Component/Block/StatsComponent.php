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

#[AsTwigComponent('block-stats')]
class StatsComponent
{
   /**
    * Unique identifier for the stats component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the stats component.
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
    * Position of the text relative to numbers (bottom, top).
    *
    * @var string
    */
   public string $text_position = 'bottom';

   /**
    * Array of statistics configurations.
    * Each stat contains: {number, text}
    *
    * @var array
    */
   public array $stats = [];

   /**
    * Constructor for the Stats component.
    * Automatically generates a unique ID with 'stats-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('stats-');
   }
}
