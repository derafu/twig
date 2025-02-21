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

#[AsTwigComponent('block-timeline')]
class TimelineComponent
{
   /**
    * Unique identifier for the timeline component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the timeline component.
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
    * Position of the timeline line (center, left, right).
    *
    * @var string
    */
   public string $line_position = 'center';

   /**
    * Array of timeline events configurations.
    *
    * Each event contains:
    * - date: Event date (YYYY-MM-DD, YYYYMM, YYYY)
    * - title: Event title (optional)
    * - description: Event description (required)
    * - icon: Font Awesome icon class (e.g. "fa-solid fa-check-circle")
    *
    * @var array
    */
   public array $events = [];

   /**
    * Optional date format for event dates.
    *
    * @var string|null
    */
   public ?string $date_format = null;

   /**
    * Constructor for the Timeline component.
    * Automatically generates a unique ID with 'timeline-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('timeline-');
   }
}
