<?php

declare(strict_types=1);

namespace Derafu\Twig\Component\Block;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('block-alert')]
class AlertComponent
{
   /**
    * Unique identifier for the alert component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the alert component.
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
    * Alert configuration array with optional variables.
    *
    * Optional variables for each alert:
    * - type: Alert type (optional, e.g., 'primary', 'secondary', 'success')
    *   Default: 'primary'
    * - content: Alert content (optional, supports HTML)
    * - icon: Optional Font Awesome icon class
    *   (e.g., "fa-solid fa-house")
    *
    * @var array{
    *     type?: string,
    *     content?: string,
    *     icon?: string
    * }[]
    */
   public array $alert = [];

   /**
    * Constructor for the Alert component.
    * Automatically generates a unique ID with 'alert-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('alert-');
   }
}