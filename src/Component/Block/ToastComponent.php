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
* Toast Component for displaying notification messages.
*
* This component creates Bootstrap-based toast notifications that can be themed
* and configured with different types, titles, content and icons.
*/
#[AsTwigComponent('block-toast')]
class ToastComponent
{
   /**
    * Unique identifier for the toast component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the toast component.
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
    * Configuration array for toast notifications.
    *
    * Structure:
    * - type: Toast type (optional, default: 'primary')
    * - title: Toast title (optional)
    * - content: Message text (optional)
    * - time: Time display (optional)
    * - icon: Font Awesome icon class (optional)
    *
    * Example:
    * [
    *   {
    *     'type' => 'success',
    *     'title' => 'Success',
    *     'content' => 'Operation completed',
    *     'time' => 'Now',
    *     'icon' => 'fas fa-check'
    *   }
    * ]
    *
    * @var array
    */
   public array $toast = [];

   /**
    * Constructor for the Toast component.
    * Automatically generates a unique ID with 'toast-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('toast-');
   }
}