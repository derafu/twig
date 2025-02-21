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

#[AsTwigComponent('block-steps')]
class StepsComponent
{
   /**
    * Unique identifier for the steps component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the steps component.
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
    * Array of steps with their configurations.
    *
    * Each step contains:
    * - icon: Font Awesome icon class (e.g. "fa-solid fa-list")
    * - title: Step title
    * - description: Step description (supports HTML)
    *
    * @var array
    */
   public array $steps = [];

   /**
    * Style of arrows connecting steps (curved, straight).
    *
    * @var string
    */
   public string $arrow_type = 'curved';

   /**
    * Constructor for the Steps component.
    * Automatically generates a unique ID with 'steps-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('steps-');
   }
}
