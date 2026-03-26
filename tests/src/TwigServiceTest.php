<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig;

use Derafu\Twig\Cache\CacheItemPool;
use Derafu\Twig\Provider\AllComponentProvider;
use Derafu\Twig\Provider\DirectoryComponentProvider;
use Derafu\Twig\Service\ComponentRegistrar;
use Derafu\Twig\Service\TwigCreator;
use Derafu\Twig\Service\TwigService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TwigService::class)]
#[CoversClass(CacheItemPool::class)]
#[CoversClass(AllComponentProvider::class)]
#[CoversClass(DirectoryComponentProvider::class)]
#[CoversClass(ComponentRegistrar::class)]
#[CoversClass(TwigCreator::class)]
class TwigServiceTest extends TestCase
{
    public function testRenderFromString(): void
    {
        $twigService = new TwigService(['extra' => false]);

        $template = 'Hello {{ name }}';
        $data = ['name' => 'World'];
        $html = $twigService->renderFromString($template, $data);
        $this->assertSame('Hello World', $html);
    }
}
