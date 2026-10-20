<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Fixture\Component;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * A component that does not say its name: it is the one of its class.
 */
#[AsTwigComponent]
final class UnnamedComponent
{
}
