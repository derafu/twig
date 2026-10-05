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

use Derafu\Translation\TranslatorFactory;
use Derafu\Twig\Cache\CacheItemPool;
use Derafu\Twig\Extension\TranslationExtension;
use Derafu\Twig\NodeVisitor\TranslationDefaultDomainNodeVisitor;
use Derafu\Twig\Provider\AllComponentProvider;
use Derafu\Twig\Provider\DirectoryComponentProvider;
use Derafu\Twig\Service\ComponentRegistrar;
use Derafu\Twig\Service\TwigCreator;
use Derafu\Twig\Service\TwigService;
use Derafu\Twig\TokenParser\TransDefaultDomainTokenParser;
use Derafu\Twig\TokenParser\TransTokenParser;
use Derafu\Twig\Translation\TwigTranslationResourceProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TwigService::class)]
#[CoversClass(CacheItemPool::class)]
#[CoversClass(AllComponentProvider::class)]
#[CoversClass(DirectoryComponentProvider::class)]
#[CoversClass(ComponentRegistrar::class)]
#[CoversClass(TwigCreator::class)]
#[UsesClass(TranslationExtension::class)]
#[UsesClass(TwigTranslationResourceProvider::class)]
#[UsesClass(TranslationDefaultDomainNodeVisitor::class)]
#[UsesClass(TransTokenParser::class)]
#[UsesClass(TransDefaultDomainTokenParser::class)]
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

    public function testTheTranslationExtensionIsAlwaysRegistered(): void
    {
        $twig = (new TwigCreator())->create(['extra' => false]);

        $this->assertTrue($twig->hasExtension(TranslationExtension::class));
        $this->assertSame('Close', $twig->createTemplate("{{ 'Close'|trans }}")->render());
    }

    public function testTheExtensionOfTheApplicationIsTheOneThatIsUsed(): void
    {
        $translator = TranslatorFactory::create('es', [], [new TwigTranslationResourceProvider()]);
        $extension = new TranslationExtension($translator, 'errors', 'es');

        $twig = (new TwigCreator())->create(['extra' => false, 'extensions' => [$extension]]);

        $this->assertSame($extension, $twig->getExtension(TranslationExtension::class));
        $this->assertSame(
            'El título del ítem es obligatorio.',
            $twig->createTemplate("{{ 'Item title is required.'|trans }}")->render()
        );
    }
}
