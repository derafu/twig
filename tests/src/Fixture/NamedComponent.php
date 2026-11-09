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

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * A component whose attribute defines its name with a named argument.
 */
#[AsTwigComponent(name: 'named-component', template: 'components/unnamedcomponent.html.twig')]
final class NamedComponent
{
}
