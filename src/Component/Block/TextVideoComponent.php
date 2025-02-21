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
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('block-text-video')]
class TextVideoComponent
{
   /**
    * Unique identifier for the text-video component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the text-video component.
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
    * Position of the video (right, left).
    *
    * @var string
    */
   public string $video_position = 'right';

   /**
    * Number of columns for text section (Bootstrap grid).
    *
    * @var int
    */
   public int $text_cols = 7;

   /**
    * Number of columns for video section (Bootstrap grid).
    *
    * @var int
    */
   public int $video_cols = 5;

   /**
    * Title of the text section.
    *
    * @var string
    */
   public string $title;

   /**
    * Main content text (supports paragraphs).
    *
    * @var string
    */
   public string $content;

   /**
    * Array of button configurations.
    * Each button contains: {text, url}
    *
    * @var array
    */
   public array $buttons = [];

   /**
    * YouTube video URL.
    *
    * @var string
    */
   public string $video_url;

   /**
    * Video aspect ratio (16:9, 4:3, 1:1).
    *
    * @var string
    */
   public string $aspect_ratio = '16:9';

   /**
    * Show video player controls.
    *
    * @var bool
    */
   public bool $show_controls = true;

   /**
    * Show video title.
    *
    * @var bool
    */
   public bool $show_title = false;

   /**
    * Use privacy-enhanced mode (youtube-nocookie.com).
    *
    * @var bool
    */
   public bool $enable_privacy = true;

   /**
    * Process component data before mount.
    * Converts YouTube URL to embed format.
    */
   #[PreMount]
   public function preMount(array $data): array
   {
       if (isset($data['video_url'])) {
           preg_match(
               '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/',
               $data['video_url'],
               $matches
           );
           $videoId = $matches[1] ?? '';

           $privacy = $data['enable_privacy'] ?? true;
           $base = $privacy
               ? 'https://www.youtube-nocookie.com/embed/'
               : 'https://www.youtube.com/embed/';

           $data['video_url'] = $base . $videoId;
       }

       return $data;
   }

   /**
    * Constructor for the Text-Video component.
    * Automatically generates a unique ID with 'text-video-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('text-video-');
   }
}
