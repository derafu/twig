<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Service;

use Derafu\Twig\Contract\ComponentRegistrarInterface;
use Derafu\Twig\Contract\TwigCreatorInterface;
use Derafu\Twig\Extension\MarkdownExtension;
use Twig\Environment;
use Twig\Extra\CssInliner\CssInlinerExtension;
use Twig\Extra\Inky\InkyExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\Extra\String\StringExtension;
use Twig\Loader\FilesystemLoader;

class TwigCreator implements TwigCreatorInterface
{
    /**
     * Options for creating the Twig instance.
     *
     * @var array
     */
    private array $options;

    private ComponentRegistrarInterface $componentRegistrar;

    /**
     * Service constructor that creates a Twig renderer instance.
     *
     * @param array $options Options for constructing the Twig instance.
     * @param ComponentRegistrarInterface|null $componentRegistrar
     */
    public function __construct(
        array $options = [],
        ?ComponentRegistrarInterface $componentRegistrar = null
    ) {
        if (!empty($options)) {
            $this->options = $this->resolveOptions($options);
        }

        $this->componentRegistrar = $componentRegistrar ?? new ComponentRegistrar();
    }

    /**
     * Create, initialize, and return a new Twig instance.
     *
     * @param array $options
     * @return Environment
     */
    public function create(array $options = []): Environment
    {
        // Resolve the options for creation.
        $options = $this->resolveOptions($options);

        // Create loader with the paths where templates will be searched.
        $loader = new FilesystemLoader($options['paths']);

        // Create the Twig renderer.
        $twig = new Environment($loader);

        // Add the extensions that are registered.
        foreach ($options['extensions'] as $extension) {
            $twig->addExtension($extension);
        }

        // Add extra extensions depending on availability.
        if ($options['extra']) {
            $this->addExtraExtensions($twig);
        }

        // Register everything related to components in Twig.
        if (!empty($options['components'])) {
            $this->componentRegistrar->register($twig, $options['components']);
        }

        // Return the Twig instance.
        return $twig;
    }

    private function resolveOptions(array $options): array
    {
        $options = array_merge($this->options ?? [], $options);

        $paths = $options['paths'] ?? [realpath(__DIR__ . '/../../templates')];
        $options['paths'] = is_string($paths) ? [$paths] : $paths;
        $options['extensions'] = $options['extensions'] ?? [];
        $options['components'] = $options['components'] ?? [];
        $options['extra'] = $options['extra'] ?? true;

        return $options;
    }

    /**
     * Loads extensions only if their dependencies are installed.
     *
     * @param Environment $twig Twig instance where extensions will be added.
     * @return void
     */
    private function addExtraExtensions(Environment $twig): void
    {
        $twig->addExtension(new MarkdownExtension());

        if (class_exists(CssInlinerExtension::class)) {
            $twig->addExtension(new CssInlinerExtension());
        }

        if (class_exists(IntlExtension::class)) {
            $twig->addExtension(new IntlExtension());
        }

        if (class_exists(StringExtension::class)) {
            $twig->addExtension(new StringExtension());
        }

        if (class_exists(InkyExtension::class)) {
            $twig->addExtension(new InkyExtension());
        }
    }
}
