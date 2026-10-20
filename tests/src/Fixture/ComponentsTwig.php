<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Fixture;

use Derafu\TestsTwig\Fixture\Component\AlertComponent;
use Derafu\Twig\Service\ComponentRegistrar;
use Derafu\Twig\Service\TwigCreator;
use Derafu\Twig\Service\TwigService;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Twig\Extension\ExtensionInterface;

/**
 * A Twig service with the component of the tests (`<twig:test-alert>`), as an
 * application registers the ones it uses.
 */
final class ComponentsTwig
{
    /**
     * The templates of the component of the tests.
     */
    public const TEMPLATES = __DIR__ . '/../../fixtures/templates';

    /**
     * @param array{paths?: string|array<string>, extensions?: iterable<ExtensionInterface>, extra?: bool, ...} $options
     * The options of the Twig service.
     */
    public static function service(array $options = []): TwigService
    {
        // The configuration of the components is not shared with other services
        // (the cache of the registrar is the same for all by default).
        return new TwigService(new TwigCreator(
            [...$options, 'components' => [AlertComponent::class]],
            new ComponentRegistrar(new ArrayAdapter())
        ));
    }
}
