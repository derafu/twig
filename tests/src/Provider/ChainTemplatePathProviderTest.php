<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Provider;

use Derafu\Twig\Contract\TemplatePathProviderInterface;
use Derafu\Twig\Provider\ChainTemplatePathProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ChainTemplatePathProvider::class)]
final class ChainTemplatePathProviderTest extends TestCase
{
    public function testPathsFollowProviderOrderAndDuplicatesKeepTheirFirstPosition(): void
    {
        $first = new class () implements TemplatePathProviderInterface {
            public function getPaths(): array
            {
                return ['/site', '/shared'];
            }
        };
        $second = new class () implements TemplatePathProviderInterface {
            public function getPaths(): array
            {
                return ['/core', '/shared'];
            }
        };
        self::assertSame(['/site', '/shared', '/core'], (new ChainTemplatePathProvider([$first, $second]))->getPaths());
    }

    public function testWithoutProvidersThereAreNoContributedPaths(): void
    {
        self::assertSame([], (new ChainTemplatePathProvider())->getPaths());
    }
}
