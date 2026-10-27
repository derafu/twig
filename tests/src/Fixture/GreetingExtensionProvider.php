<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Fixture;

use Derafu\Twig\Contract\ExtensionProviderInterface;
use Derafu\Twig\Contract\TwigServiceInterface;

/**
 * Depends on Twig without creating the environment in its constructor.
 */
final class GreetingExtensionProvider implements ExtensionProviderInterface
{
    public function __construct(private readonly TwigServiceInterface $twig)
    {
    }

    public function getExtensions(array $options = []): iterable
    {
        yield new GreetingExtension($this->twig);
    }
}
