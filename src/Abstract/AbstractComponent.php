<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.org>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Abstract;

use ReflectionClass;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

/**
 * Abstract base class for Twig components.
 *
 * This class provides common functionality for all Twig components including:
 *
 *   - Unique ID generation.
 *   - Style management.
 *   - Container class handling.
 */
abstract class AbstractComponent
{
    /**
     * Prefix of the theme CSS class.
     *
     * @var string
     */
    protected const THEME_PREFIX = 'derafu-theme-';

    /**
     * Unique identifier for the component instance.
     *
     * Automatically generated in the constructor using the component's prefix.
     *
     * @var string
     */
    #[ExposeInTemplate]
    protected string $id;

    /**
     * CSS classes for styling the component instance.
     *
     *  Can be a single class or multiple space-separated classes.
     *
     * @var string|null
     */
    #[ExposeInTemplate]
    protected ?string $style = null;

    /**
     * Container class for wrapping the component content.
     *
     * @var string|null
     */
    protected ?string $container = null;

    /**
     * Theme class for the component, without `derafu-theme-` prefix.
     *
     * @var string|null
     */
    protected ?string $theme = null;

    /**
     * Initializes a new instance of the component.
     *
     * Automatically generates a unique ID using the component's name.
     */
    public function __construct()
    {
        $this->id = $this->generateUniqueId();
    }

    /**
     * Sets the component's unique identifier.
     *
     * @param string $id The component's unique identifier.
     * @return static For method chaining.
     */
    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the component's unique identifier.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Sets the component's CSS classes.
     *
     * @param string $style Space-separated CSS classes.
     * @return static For method chaining.
     */
    public function setStyle(string $style): static
    {
        $this->style = $style;

        return $this;
    }

    /**
     * Gets the component's CSS classes.
     *
     * @return string
     */
    public function getStyle(): string
    {
        $theme = $this->theme ? (static::THEME_PREFIX . $this->theme) : '';

        return trim($theme . ' ' . $this->container . ' ' . $this->style);
    }

    /**
     * Sets the container class.
     *
     * @param string $container Container CSS class.
     * @return static For method chaining.
     */
    public function setContainer(string $container): static
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Sets the container theme.
     *
     * @param string $theme Container CSS theme class, without `derafu-theme-`.
     * @return static For method chaining.
     */
    public function setTheme(string $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Generates a unique identifier for the component instance.
     *
     * Uses the component's prefix followed by a unique string.
     *
     * @return string
     */
    protected function generateUniqueId(): string
    {
        return uniqid($this->getComponentName() . '--');
    }

    /**
     * Gets the component name.
     *
     * Extracts the name from the AsTwigComponent attribute,
     *
     * Falls back to the lowercase class name if no attribute is found.
     *
     * @return static
     */
    protected function getComponentName(): string
    {
        $reflection = new ReflectionClass($this);
        $attributes = $reflection->getAttributes();

        foreach ($attributes as $attribute) {
            if ($attribute->getName() === AsTwigComponent::class) {
                $args = $attribute->getArguments();
                return $args[0];
            }
        }

        return strtolower(basename(str_replace('\\', '__', get_class($this))));
    }

    protected function getDefaultIcon(?string $type): string
    {
        return match($type) {
            'primary' => 'fa-solid fa-star text-primary fa-fw',
            'secondary' => 'fa-solid fa-info text-secondary fa-fw',
            'success' => 'fa-solid fa-check-circle text-success fa-fw',
            'danger' => 'fa-solid fa-triangle-exclamation text-danger fa-fw',
            'warning' => 'fa-solid fa-exclamation-circle text-warning fa-fw',
            'info' => 'fa-solid fa-info-circle text-info fa-fw',
            'light' => 'fa-solid fa-sun text-dark fa-fw',
            'dark' => 'fa-solid fa-moon text-dark fa-fw',
            default => 'fa-solid fa-exclamation fa-fw',
        };
    }
}
