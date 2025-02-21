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

/**
* Video Component for displaying embedded videos with additional content.
*
* This component creates customizable video blocks that can include
* title, description, action buttons and various display options.
*/
#[AsTwigComponent('block-video')]
class VideoComponent
{
   /**
    * Unique identifier for the video component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the video component.
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
    * Configuration array for video content.
    *
    * Structure:
    * - title: Video title (required)
    * - video: Video URL (required, supports YouTube URLs)
    * - description: Video description (optional, supports HTML)
    * - buttons: Array of action buttons (optional)
    *   - text: Button text
    *   - url: Button URL
    * - size: Video size (normal, small, default: normal)
    * - align: Video alignment (left, center, right, default: center)
    *
    * @var array
    */
   public array $video = [];

   /**
    * Constructor for the Video component.
    * Automatically generates a unique ID with 'video-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('video-');
   }
}