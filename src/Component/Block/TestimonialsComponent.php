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

#[AsTwigComponent('block-testimonials')]
class TestimonialsComponent
{
   /**
    * Unique identifier for the testimonials component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the testimonials component.
    *
    * @var string
    */
   public string $theme = 'default';

   /**
    * Use container wrapper.
    *
    * @var string
    */
   public string $container = '';

   /**
    * Array of testimonials configurations.
    *
    * Each testimonial contains:
    * - image: Background image URL
    * - quote: Testimonial text
    * - author: Author name
    * - company: Company name (optional)
    * - url: Link URL (optional)
    * - position: Card position (center, left, right)
    *
    * @var array
    */
   public array $testimonials = [];

   /**
    * Enable automatic slideshow.
    *
    * @var bool
    */
   public bool $autoplay = true;

   /**
    * Time between slides in milliseconds.
    *
    * @var int
    */
   public int $interval = 5000;

   /**
    * Show navigation controls.
    *
    * @var bool
    */
   public bool $showControls = true;

   /**
    * Show slide indicators.
    *
    * @var bool
    */
   public bool $showIndicators = true;

   /**
    * Maximum width of testimonial cards in pixels.
    *
    * @var int
    */
   public int $maxWidth = 600;

   /**
    * Background image brightness percentage.
    *
    * @var int
    */
   public int $brightness = 70;

   /**
    * Height of testimonial section in pixels.
    *
    * @var int
    */
   public int $height = 500;

   /**
    * Recommended width for background images.
    */
   public const RECOMMENDED_IMAGE_WIDTH = 1920;

   /**
    * Recommended height for background images.
    */
   public const RECOMMENDED_IMAGE_HEIGHT = 500;

   /**
    * Constructor for the Testimonials component.
    * Automatically generates a unique ID with 'testimonials-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('testimonials-');
   }
}
