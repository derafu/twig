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

use Derafu\Twig\Abstract\AbstractComponent;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Modal Component for displaying popup dialogs in the application.
 *
 * This component creates a Bootstrap-based modal dialog that can be themed and configured
 * with different content, buttons, and styles.
 */
#[AsTwigComponent('block-modal')]
class ModalComponent extends AbstractComponent
{
     /**
      * Configuration array for the modal content and behavior.
      *
      * Optional variables:
      * - title: Optional modal title (string)
      * - content: Optional modal content (string)
      * - button_primary: Optional primary button
      *   {
      *     text: Button text (string),
      *     action: Button action (string, optional)
      *   }
      * - button_secondary: Optional secondary button
      *   {
      *     text: Button text (string),
      *     action: Button action (string, optional)
      *   }
      *
      * @var array{
      *     title?: ?string,
      *     content?: ?string,
      *     button_primary?: ?array{text: string, action?: string},
      *     button_secondary?: ?array{text: string, action?: string}
      * }
      */
    public array $modal = [];
}
