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

#[AsTwigComponent('block-header')]
class HeaderComponent
{
   /**
    * Unique identifier for the header component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the header component.
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
    * URL for the logo link.
    *
    * @var string
    */
   public string $logoUrl = '';

   /**
    * Path to the logo image file.
    *
    * @var string|null
    */
   public ?string $logoImage = null;

   /**
    * HTML content for the logo.
    *
    * @var string|null
    */
   public ?string $logoHtml = null;

   /**
    * Maximum width for the logo in pixels.
    *
    * @var int
    */
   public int $logoMaxWidth = 150;

   /**
    * Array of navigation items for the left side.
    *
    * @var array
    */
   public array $leftNav = [];

   /**
    * Array of navigation items for the right side.
    *
    * @var array
    */
   public array $rightNav = [];

   /**
    * Icon class for the call-to-action button.
    *
    * @var string|null
    */
   public ?string $ctaIcon = null;

   /**
    * Text for the call-to-action button.
    *
    * @var string|null
    */
   public ?string $ctaText = null;

   /**
    * URL for the call-to-action button.
    *
    * @var string|null
    */
   public ?string $ctaUrl = null;

   /**
    * Trigger type for dropdown menus ('click' or 'hover').
    *
    * @var string
    */
   public string $dropdownTrigger = 'click';

   /**
    * Position of the header ('normal' or 'fixed').
    *
    * @var string
    */
   public string $position = 'normal';

   /**
    * Background color for the header.
    *
    * @var string|null
    */
   public ?string $backgroundColor = null;

   /**
    * Process navigation items before component mount.
    */
   #[PreMount]
   public function preMount(array $data): array
   {
       if (isset($data['leftNav'])) {
           $data['leftNav'] = array_map([$this, 'processNavItem'], $data['leftNav']);
       }

       if (isset($data['rightNav'])) {
           $data['rightNav'] = array_map([$this, 'processNavItem'], $data['rightNav']);
       }

       return $data;
   }

   /**
    * Process a single navigation item to determine its type.
    */
   private function processNavItem(array $item): array
   {
       if (isset($item['sections'])) {
           $item['type'] = 'mega-dropdown';
       } elseif (isset($item['items'])) {
           $item['type'] = 'dropdown';
           $item['items'] = array_map(function ($subitem) {
               if (isset($subitem['items'])) {
                   $subitem['type'] = 'submenu';
               } elseif (!isset($subitem['type'])) {
                   $subitem['type'] = 'link';
               }
               return $subitem;
           }, $item['items']);
       } else {
           $item['type'] = 'link';
       }

       return $item;
   }

   /**
    * Constructor for the Header component.
    * Automatically generates a unique ID with 'header-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('header-');
   }
}
