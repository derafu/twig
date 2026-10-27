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

use Derafu\Twig\Extension\MarkdownExtension;
use Derafu\Twig\Extension\TranslationExtension;
use Derafu\Twig\NodeVisitor\TranslationDefaultDomainNodeVisitor;
use Derafu\Twig\Provider\DefaultExtensionProvider;
use Derafu\Twig\TokenParser\TransDefaultDomainTokenParser;
use Derafu\Twig\TokenParser\TransTokenParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Loader\ArrayLoader as TranslationArrayLoader;
use Symfony\Component\Translation\Translator;
use Twig\Environment;
use Twig\Extra\CssInliner\CssInlinerExtension;
use Twig\Extra\Inky\InkyExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\Extra\String\StringExtension;
use Twig\Loader\ArrayLoader;

#[CoversClass(DefaultExtensionProvider::class)]
#[UsesClass(TranslationExtension::class)]
#[UsesClass(MarkdownExtension::class)]
#[UsesClass(TranslationDefaultDomainNodeVisitor::class)]
#[UsesClass(TransDefaultDomainTokenParser::class)]
#[UsesClass(TransTokenParser::class)]
final class DefaultExtensionProviderTest extends TestCase
{
    public function testDisabledExtrasKeepTheTranslationExtension(): void
    {
        $extensions = iterator_to_array((new DefaultExtensionProvider())->getExtensions(['extra' => false]));
        self::assertCount(1, $extensions);
        self::assertInstanceOf(TranslationExtension::class, $extensions[0]);
    }

    public function testEnabledExtrasIncludeTheInstalledOptionalExtensions(): void
    {
        $extensions = iterator_to_array((new DefaultExtensionProvider())->getExtensions());
        self::assertSame([
            TranslationExtension::class,
            MarkdownExtension::class,
            CssInlinerExtension::class,
            IntlExtension::class,
            StringExtension::class,
            InkyExtension::class,
        ], array_map(static fn ($extension): string => $extension::class, $extensions));
    }

    public function testTheSuppliedTranslatorIsUsedByTheDefaultExtension(): void
    {
        $translator = new Translator('es');
        $translator->addLoader('array', new TranslationArrayLoader());
        $translator->addResource('array', ['Hello' => 'Hola'], 'es');
        $twig = new Environment(new ArrayLoader());
        foreach ((new DefaultExtensionProvider($translator))->getExtensions(['extra' => false]) as $extension) {
            $twig->addExtension($extension);
        }
        self::assertSame('Hola', $twig->createTemplate('{{ "Hello"|trans }}')->render());
    }
}
