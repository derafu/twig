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
use Symfony\UX\TwigComponent\Attribute\PostMount;
use Symfony\UX\TwigComponent\Attribute\PreMount;

/**
 * A component that changes its data before and after they are set.
 */
#[AsTwigComponent('test-greeting')]
final class GreetingComponent
{
    public string $name = '';

    public string $greeting = '';

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    #[PreMount]
    public function upperName(array $data): array
    {
        if (isset($data['name']) && is_string($data['name'])) {
            $data['name'] = strtoupper($data['name']);
        }

        return $data;
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    #[PostMount]
    public function greet(array $data): array
    {
        $this->greeting = 'Hello ' . $this->name;

        return $data;
    }
}
