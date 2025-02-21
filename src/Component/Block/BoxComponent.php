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

#[AsTwigComponent('block-box')]
class BoxComponent
{
   /**
    * Unique identifier for the box component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the box component.
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
    * Title of the box component.
    *
    * @var string
    */
   public string $title = '';

   /**
    * Content text of the box (supports HTML).
    *
    * @var string
    */
   public string $text = '';

   /**
    * Constructor for the Box component.
    * Automatically generates a unique ID with 'box-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('box-');
   }
}