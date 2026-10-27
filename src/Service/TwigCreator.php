<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Service;

use Derafu\Twig\Contract\ComponentProviderInterface;
use Derafu\Twig\Contract\ComponentRegistrarInterface;
use Derafu\Twig\Contract\ExtensionProviderInterface;
use Derafu\Twig\Contract\TwigCreatorInterface;
use Derafu\Twig\Provider\DefaultExtensionProvider;
use Derafu\Twig\Support\ProjectDirectory;
use Twig\Environment;
use Twig\Extension\ExtensionInterface;
use Twig\Loader\FilesystemLoader;

class TwigCreator implements TwigCreatorInterface
{
    /**
     * Options for creating the Twig instance.
     *
     * @var array{paths: array<string>, extensions: iterable<ExtensionInterface>, components: ComponentProviderInterface|iterable<class-string|object>, extra: bool, ...}
     */
    private array $options;

    private ComponentRegistrarInterface $componentRegistrar;

    /**
     * Service constructor that creates a Twig renderer instance.
     *
     * @param array{paths?: string|array<string>, extensions?: iterable<ExtensionInterface>, components?: ComponentProviderInterface|iterable<class-string|object>, extra?: bool, ...} $options Options for constructing the Twig instance.
     * @param ComponentRegistrarInterface|null $componentRegistrar
     * @param iterable<ExtensionProviderInterface> $extensionProviders Highest priority first; the first extension of each class wins.
     * @param iterable<ComponentProviderInterface> $componentProviders Providers of component classes.
     */
    public function __construct(
        array $options = [],
        ?ComponentRegistrarInterface $componentRegistrar = null,
        private readonly iterable $extensionProviders = [],
        private readonly iterable $componentProviders = [],
    ) {
        if (!empty($options)) {
            $this->options = $this->resolveOptions($options);
        }

        $this->componentRegistrar = $componentRegistrar ?? new ComponentRegistrar();
    }

    /**
     * Create, initialize, and return a new Twig instance.
     *
     * @param array{paths?: string|array<string>, extensions?: iterable<ExtensionInterface>, components?: ComponentProviderInterface|iterable<class-string|object>, extra?: bool, ...} $options
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

        // Defer iterating providers until environment creation. This allows an
        // extension to depend on a service which uses this Twig service.
        $hasProviders = false;
        foreach ($this->extensionProviders as $provider) {
            $hasProviders = true;
            $this->addProviderExtensions($twig, $provider, $options);
        }
        if (!$hasProviders) {
            $this->addProviderExtensions($twig, new DefaultExtensionProvider(), $options);
        }

        $components = $options['components'] instanceof ComponentProviderInterface
            ? $options['components']->getComponents()
            : [...$options['components']];
        foreach ($this->componentProviders as $provider) {
            $components = [...$components, ...$provider->getComponents()];
        }
        if ($components !== []) {
            $this->componentRegistrar->register($twig, $components);
        }

        // Return the Twig instance.
        return $twig;
    }

    /**
     * @param array{paths?: string|array<string>, extensions?: iterable<ExtensionInterface>, components?: ComponentProviderInterface|iterable<class-string|object>, extra?: bool, ...} $options
     * @return array{paths: array<string>, extensions: iterable<ExtensionInterface>, components: ComponentProviderInterface|iterable<class-string|object>, extra: bool, ...}
     */
    private function resolveOptions(array $options): array
    {
        $options = array_merge($this->options ?? [], $options);

        $paths = $options['paths'] ?? $this->defaultPaths();
        $options['paths'] = is_string($paths) ? [$paths] : $paths;
        $options['extensions'] = $options['extensions'] ?? [];
        $options['components'] = $options['components'] ?? [];
        $options['extra'] = $options['extra'] ?? true;

        return $options;
    }

    /**
     * The paths of the templates when they are not given: the `templates`
     * directory of the project that is running, if it has one.
     *
     * @return array<string>
     */
    private function defaultPaths(): array
    {
        $templates = ProjectDirectory::path() . '/templates';

        return is_dir($templates) ? [$templates] : [];
    }

    /**
     * @param array{extra?: bool, ...} $options
     */
    private function addProviderExtensions(
        Environment $twig,
        ExtensionProviderInterface $provider,
        array $options
    ): void {
        foreach ($provider->getExtensions($options) as $extension) {
            if (!$twig->hasExtension($extension::class)) {
                $twig->addExtension($extension);
            }
        }
    }
}
