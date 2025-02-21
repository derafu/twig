<?php

declare(strict_types=1);

namespace Derafu\Twig\Component\Block;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Title Component for displaying customizable headings.
 *
 * This component creates headings with optional subtitle and styling options
 * like alignment and bottom border.
 */
#[AsTwigComponent('block-title')]
class TitleComponent
{
   /**
    * Unique identifier for the title component.
    *
    * @var string
    */
   public string $id;

   /**
    * Theme for styling the title component.
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
    * Title configuration array with optional variables.
    *
    * Optional variables for each title:
    * - title:
    *   - text: Optional title text (supports HTML)
    *   - level: Optional heading level (h1-h6, default: h2)
    *   - alignment: Optional text alignment (left, center, right, default: center)
    *   - bottomBorder: Optional bottom border (true/false, default: false)
    *   - subtitle: Optional subtitle text (supports HTML)
    *
    * @var array{
    *     title?: ?array{
    *         text?: ?string,
    *         level?: ?string,
    *         alignment?: ?string,
    *         bottomBorder?: ?bool,
    *         subtitle?: ?string
    *     }
    * }[]
    */
   public array $title = [];

   /**
    * Constructor for the Title component.
    * Automatically generates a unique ID with 'title-' prefix.
    */
   public function __construct()
   {
       $this->id = uniqid('title-');
   }
}
