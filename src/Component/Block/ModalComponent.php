<?php

declare(strict_types=1);

namespace Derafu\Twig\Component\Block;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Modal Component for displaying popup dialogs in the application.
 *
 * This component creates a Bootstrap-based modal dialog that can be themed and configured
 * with different content, buttons, and styles.
 */
#[AsTwigComponent('block-modal')]
class ModalComponent
{
     /**
     * Unique identifier for the modal element.
     * Used for Bootstrap modal functionality and DOM targeting.
     *
     * @var string
     */
    public string $id;

     /**
     * Theme name for styling the modal.
     * Defaults to 'default' theme.
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

    /**
     * Constructor for the Modal component.
     * Automatically generates a unique ID with 'modal-' prefix.
     */
    public function __construct()
    {
        $this->id = uniqid('modal-');
    }
}