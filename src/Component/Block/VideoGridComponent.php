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
* Video Grid Component for displaying multiple videos in a grid layout.
*
* This component creates a responsive grid of video blocks with optional
* titles, descriptions, and action buttons.
*/
#[AsTwigComponent('block-video-grid')]
class VideoGridComponent
{
   /**
    * Unique identifier for the video grid component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the video grid component.
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
    * Array of videos with their configurations.
    *
    * Each video contains:
    * - title: Video title (optional)
    * - video: YouTube URL (required)
    * - description: Video description (optional, supports HTML)
    * - buttons: Array of action buttons (optional)
    *   - text: Button text
    *   - url: Button URL
    * - card: Display as card (optional, default: true)
    *
    * @var array
    */
   public array $video = [];

   /**
    * Number of columns in the grid layout.
    *
    * @var int
    */
   public int $columns = 2;

   /**
    * Constructor for the Video Grid component.
    * Automatically generates a unique ID with 'video-grid-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('video-grid-');
   }
}