<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.org>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Service;

use Derafu\Twig\Cache\CacheItemPool;
use Derafu\Twig\Contract\ComponentProviderInterface;
use Derafu\Twig\Contract\ComponentRegistrarInterface;
use Psr\Cache\CacheItemPoolInterface;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PostMount;
use Symfony\UX\TwigComponent\Attribute\PreMount;
use Symfony\UX\TwigComponent\ComponentFactory;
use Symfony\UX\TwigComponent\ComponentProperties;
use Symfony\UX\TwigComponent\ComponentRenderer;
use Symfony\UX\TwigComponent\ComponentStack;
use Symfony\UX\TwigComponent\ComponentTemplateFinder;
use Symfony\UX\TwigComponent\Twig\ComponentExtension;
use Symfony\UX\TwigComponent\Twig\ComponentLexer;
use Symfony\UX\TwigComponent\Twig\ComponentRuntime;
use Twig\Environment;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

class ComponentRegistrar implements ComponentRegistrarInterface
{
    public function __construct(
        private ?CacheItemPoolInterface $cache = null,
        private string $cacheKey = 'derafu_twig_components_config'
    ) {
        $this->cache = $cache ?? new CacheItemPool();
    }

    public function register(
        Environment $twig,
        ComponentProviderInterface|array $componentsProvider
    ): Environment {
        $basicServices = $this->createBasicServices($twig);

        $components = is_array($componentsProvider)
            ? $componentsProvider
            : $componentsProvider->getComponents();

        $componentServices = $this->createComponentServices(
            $twig,
            $components,
            $basicServices
        );

        return $this->registerTwigExtensions($twig, $componentServices);
    }

    private function createBasicServices(Environment $twig): array
    {
        return [
            'propertyAccessor' => PropertyAccess::createPropertyAccessor(),
            'eventDispatcher' => new EventDispatcher(),
            'templateFinder' => new ComponentTemplateFinder($twig, 'components'),
        ];
    }

    private function createComponentServices(
        Environment $twig,
        array $components,
        array $basicServices
    ): array {
        $serviceLocator = $this->createServiceLocator($components);
        $factory = $this->createComponentFactory(
            $components,
            $serviceLocator,
            $basicServices
        );

        $renderer = $this->createComponentRenderer(
            $twig,
            $factory,
            $basicServices
        );

        return [
            'serviceLocator' => $serviceLocator,
            'factory' => $factory,
            'renderer' => $renderer,
        ];
    }

    private function createServiceLocator(array $components): ServiceLocator
    {
        $services = [];
        foreach ($components as $componentClass) {
            $name = $this->getComponentName($componentClass);
            $services[$name] = fn () => new $componentClass();
        }

        return new ServiceLocator($services);
    }

    private function createComponentFactory(
        array $components,
        ServiceLocator $serviceLocator,
        array $basicServices
    ): ComponentFactory {
        return new ComponentFactory(
            $basicServices['templateFinder'],
            $serviceLocator,
            $basicServices['propertyAccessor'],
            $basicServices['eventDispatcher'],
            $this->createComponentConfig($components),
            $this->createComponentClassMap($components)
        );
    }

    private function createComponentConfig(array $components): array
    {
        if (!$this->cache) {
            return $this->buildComponentConfig($components);
        }

        $item = $this->cache->getItem($this->cacheKey);
        if ($item->isHit()) {
            return $item->get();
        }

        $config = $this->buildComponentConfig($components);
        $item->set($config);
        $this->cache->save($item);

        return $config;
    }

    private function buildComponentConfig(array $components): array
    {
        $config = [];
        foreach ($components as $componentClass) {
            $reflection = new ReflectionClass($componentClass);
            $attributes = $reflection->getAttributes(AsTwigComponent::class);

            if (empty($attributes)) {
                continue;
            }

            $attribute = $attributes[0];
            $arguments = $attribute->getArguments();
            $name = $this->getComponentName($componentClass);

            $config[$name] = [
                'key' => $name,
                'class' => $componentClass,
                'service_id' => $name,

                // Template personalizado o el por defecto.
                'template' => $arguments['template']
                    ?? $this->getTemplatePath($name),

                // Nombre de variable para atributos.
                'attributes_var' => $arguments['attributesVar']
                    ?? 'attributes',

                // Exposición de propiedades públicas.
                'expose_public_props' => $arguments['exposePublicProps']
                    ?? false,

                // PreMount methods si existen.
                'pre_mount' => $this->getPreMountMethods($reflection),

                // PostMount methods si existen.
                'post_mount' => $this->getPostMountMethods($reflection),
            ];
        }

        return $config;
    }

    private function getPreMountMethods(ReflectionClass $reflection): array
    {
        return $this->getMethodsWithAttribute($reflection, PreMount::class);
    }

    private function getPostMountMethods(ReflectionClass $reflection): array
    {
        return $this->getMethodsWithAttribute($reflection, PostMount::class);
    }

    private function getMethodsWithAttribute(
        ReflectionClass $reflection,
        string $attributeClass
    ): array {
        $methods = [];
        foreach ($reflection->getMethods() as $method) {
            if (!empty($method->getAttributes($attributeClass))) {
                $methods[] = $method->getName();
            }
        }
        return $methods;
    }

    private function createComponentClassMap(array $components): array
    {
        $map = [];

        foreach ($components as $componentClass) {
            $map[$componentClass] = $this->getComponentName($componentClass);
        }

        return $map;
    }

    private function createComponentRenderer(
        Environment $twig,
        ComponentFactory $factory,
        array $basicServices
    ): ComponentRenderer {
        return new ComponentRenderer(
            $twig,
            $basicServices['eventDispatcher'],
            $factory,
            new ComponentProperties($basicServices['propertyAccessor']),
            new ComponentStack()
        );
    }

    private function registerTwigExtensions(
        Environment $twig,
        array $services
    ): Environment {
        // Registrar ComponentExtension
        $twig->addExtension(new ComponentExtension());

        // Registrar Runtime
        $renderersLocator = new ServiceLocator([]);
        $twig->addRuntimeLoader(
            $this->createRuntimeLoader($services['renderer'], $renderersLocator)
        );

        // Registrar Lexer
        $twig->setLexer(new ComponentLexer($twig));

        return $twig;
    }

    private function createRuntimeLoader(
        ComponentRenderer $renderer,
        ServiceLocator $renderers
    ): RuntimeLoaderInterface {
        return new class ($renderer, $renderers) implements RuntimeLoaderInterface {
            public function __construct(
                private ComponentRenderer $renderer,
                private ServiceLocator $renderers
            ) {
            }

            public function load($class)
            {
                if ($class === ComponentRuntime::class) {
                    return new ComponentRuntime($this->renderer, $this->renderers);
                }
                return null;
            }
        };
    }

    private function getComponentName(string $componentClass): string
    {
        // Extraer el nombre del componente de la clase usando reflexión o atributos
        $reflection = new \ReflectionClass($componentClass);
        $attributes = $reflection->getAttributes(AsTwigComponent::class);

        if (!empty($attributes)) {
            return $attributes[0]->getArguments()[0]
                ?? strtolower(basename(str_replace('\\', '/', $componentClass)))
            ;
        }

        return strtolower(basename(str_replace('\\', '/', $componentClass)));
    }

    private function getTemplatePath(string $name): string
    {
        return sprintf('components/%s.html.twig', $name);
    }
}
