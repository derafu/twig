<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Component;

use Derafu\Translation\TranslatorFactory;
use Derafu\Twig\Extension\TranslationExtension;
use Derafu\Twig\Service\TwigService;
use Derafu\Twig\Translation\TwigTranslationResourceProvider;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The texts that the components write are in English, and they are translated if
 * the application gives its translator to the translation extension.
 */
#[CoversNothing]
final class ComponentTranslationsTest extends TestCase
{
    private function render(string $twig, array $data, bool $translated): string
    {
        $extensions = $translated
            ? [new TranslationExtension(TranslatorFactory::create('es', [], [new TwigTranslationResourceProvider()]), null, 'es')]
            : [];

        return (new TwigService([
            'extra' => false,
            'paths' => [realpath(__DIR__ . '/../../../resources/templates')],
            'extensions' => $extensions,
        ]))->renderFromString($twig, $data);
    }

    /**
     * @return array<string, array{string, array<string, mixed>, string, string}>
     */
    public static function provideTexts(): array
    {
        $testimonials = ['testimonials' => [['background' => '/img/bg.jpg', 'content' => 'Great', 'author' => 'Maria']]];

        return [
            'a label of a button' => [
                '<twig:block-alert content="Test" :dismissible="true" />',
                [],
                'aria-label="Close"',
                'aria-label="Cerrar"',
            ],
            'a label with a value' => [
                '<twig:block-testimonials :testimonials="testimonials" />',
                $testimonials,
                'aria-label="Slide 1"',
                'aria-label="Diapositiva 1"',
            ],
            'an alternative text with a value' => [
                '<twig:block-testimonials :testimonials="testimonials" />',
                $testimonials,
                'alt="Background 1"',
                'alt="Fondo 1"',
            ],
            'a text that is only for readers of screens' => [
                '<twig:block-testimonials :testimonials="testimonials" />',
                $testimonials,
                '<span class="visually-hidden">Previous</span>',
                '<span class="visually-hidden">Anterior</span>',
            ],
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('provideTexts')]
    public function testTheTextIsInEnglishAndItIsTranslated(string $twig, array $data, string $english, string $spanish): void
    {
        $this->assertStringContainsString($english, $this->render($twig, $data, false));
        $this->assertStringContainsString($spanish, $this->render($twig, $data, true));
        $this->assertStringNotContainsString($english, $this->render($twig, $data, true));
    }
}
