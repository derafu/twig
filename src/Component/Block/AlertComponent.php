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

#[AsTwigComponent('block-alert')]
class AlertComponent extends AbstractComponent
{
    /**
     * Alert type (e.g., 'primary', 'secondary', 'success')
     * Default: 'primary'
     */
    #[ExposeInTemplate()]
    public string $type = 'primary';

    /**
     * Alert content (supports HTML)
     */
    #[ExposeInTemplate()]
    public string $content;

    /**
     * Optional Font Awesome icon class (e.g., "fa-solid fa-house")
     */
    #[ExposeInTemplate()]
    protected ?string $icon = null;

    /**
     * Container class for the alert component.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $container = null;

    /**
     * Unique identifier for the alert component.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $id = '';

    /**
     * Additional CSS classes for the alert component.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $class = null;

    /**
     * Get the value of icon.
     */
    public function getIcon()
    {
        if (!isset($this->icon)) {
            $this->icon = $this->getDefaultIcon($this->type);
        };

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
