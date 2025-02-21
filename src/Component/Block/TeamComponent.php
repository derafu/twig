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

#[AsTwigComponent('block-team')]
class TeamComponent
{
   /**
    * Unique identifier for the team component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the team component.
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
    * Content alignment (left, center, right).
    *
    * @var string
    */
   public string $align = 'center';

   /**
    * Array of team members configurations.
    *
    * Each member contains:
    * - image: Member photo URL
    * - name: Member name
    * - role: Member role/position
    * - bio: Member biography (optional)
    * - links: Array of social/contact links:
    *   - icon: Link icon (optional)
    *   - text: Link text
    *   - url: Link URL
    *
    * @var array
    */
   public array $members = [];

   /**
    * Constructor for the Team component.
    * Automatically generates a unique ID with 'team-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('team-');
   }
}