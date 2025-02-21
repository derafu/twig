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
* Quote Component for displaying testimonials, citations, or quotes.
*
* This component creates a styled quote block that can include the quote text,
* author information, source reference, and an optional author image.
*/
#[AsTwigComponent('block-quote')]
class QuoteComponent
{
   /**
    * Unique identifier for the quote component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the quote component.
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
    * Configuration array for quote content.
    *
    * Structure:
    * - quote: Quote text (optional)
    * - author: Quote author name (optional)
    * - source: Quote source or reference (optional)
    * - image: Author image URL (optional)
    *
    * Example:
    * [
    *   {
    *     'quote' => 'The quote text',
    *     'author' => 'Author Name',
    *     'source' => 'Source Reference',
    *     'image' => '/path/to/image.jpg'
    *   }
    * ]
    *
    * @var array
    */
   public array $quote = [];

   /**
    * Constructor for the Quote component.
    * Automatically generates a unique ID with 'quote-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('quote-');
   }
}