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
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

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
     * Additional CSS classes for the modal.
     */
    #[ExposeInTemplate()]
    public ?string $class = null;

    /**
     * Container wrapper class (e.g., 'container' or 'container-fluid').
     */
    #[ExposeInTemplate()]
    public ?string $container = null;

    /**
     * Modal size (sm, lg, xl, fullscreen).
     */
    #[ExposeInTemplate()]
    public ?string $size = null;

    /**
     * Modal title (optional).
     */
    #[ExposeInTemplate()]
    public ?string $title = null;

    /**
     * Modal content (supports HTML).
     */
    #[ExposeInTemplate()]
    public string $content;

    /**
     * Show close button in header.
     */
    #[ExposeInTemplate()]
    public bool $showClose = true;

    /**
     * Static backdrop (modal won't close on backdrop click).
     */
    #[ExposeInTemplate()]
    public bool $staticBackdrop = false;

    /**
     * Modal is vertically centered.
     */
    #[ExposeInTemplate()]
    public bool $centered = false;

    /**
     * Scrollable content.
     */
    #[ExposeInTemplate()]
    public bool $scrollable = false;

    /**
     * Array of footer buttons.
     * Structure: [
     *   'text' => string,
     *   'type' => string (primary, secondary, etc),
     *   'dismiss' => bool (optional),
     *   'attributes' => string (optional, additional HTML attributes)
     * ]
     */
    #[ExposeInTemplate()]
    public array $buttons = [];
}
