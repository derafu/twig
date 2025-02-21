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

#[AsTwigComponent('block-tabs')]
class TabsComponent
{
   /**
    * Unique identifier for the tabs component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the tabs component.
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
    * Array of tab configurations.
    *
    * Each tab contains title and content.
    *
    * @var array
    */
   public array $tabs = [];

   /**
    * Identifier of the active tab. First tab is active if null.
    *
    * @var string|null
    */
   public ?string $activeTab = null;

   /**
    * Layout orientation of tabs (horizontal, vertical).
    *
    * @var string
    */
   public string $position = 'horizontal';

   /**
    * Number of columns for vertical layout.
    *
    * @var int
    */
   public int $cols = 2;

   /**
    * Constructor for the Tabs component.
    * Automatically generates a unique ID with 'tabs-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('tabs-');
   }
}
