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
 * Toast Component for displaying notification messages.
 *
 * This component creates Bootstrap-based toast notifications that can be themed
 * and configured with different types, titles, content and icons.
 */
#[AsTwigComponent('block-toast')]
class ToastComponent extends AbstractComponent
{
    /**
     * Toast type (primary, secondary, tertiary)
     * Default: 'primary'
     */
    #[ExposeInTemplate()]
    public string $type = 'primary';

    /**
     * Toast title
     */
    #[ExposeInTemplate()]
    public ?string $title = null;

    /**
     * Toast content/message
     */
    #[ExposeInTemplate()]
    public ?string $content = null;

    /**
     * Time display (e.g., "2 mins ago")
     */
    #[ExposeInTemplate()]
    public ?string $time = null;

    /**
     * Optional Font Awesome icon class
     */
    #[ExposeInTemplate()]
    protected ?string $icon = null;

    /**
     * Get the value of icon.
     */
    public function getIcon()
    {
        if (!isset($this->icon)) {
            $this->icon = $this->getDefaultIcon($this->type);
        }

        return $this->icon;
    }

    /**
     * Set the value of icon.
     *
     * @return  self
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }
}
