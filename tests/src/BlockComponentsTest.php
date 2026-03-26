<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig;

use Derafu\Twig\Service\TwigService;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
class BlockComponentsTest extends TestCase
{
    private static TwigService $service;

    public static function setUpBeforeClass(): void
    {
        self::$service = new TwigService([
            'extra' => false,
            'paths' => [realpath(__DIR__ . '/../../resources/templates')],
        ]);
    }

    #[DataProvider('provideComponents')]
    public function testBlockComponentRenders(
        string $twig,
        array $data,
        string $expectedCssClass
    ): void {
        $html = self::$service->renderFromString($twig, $data);

        $this->assertNotEmpty($html);
        $this->assertStringContainsString($expectedCssClass, $html);
    }

    public static function provideComponents(): array
    {
        return [
            'accordion' => [
                '<twig:block-accordion :items="items" />',
                ['items' => [['title' => 'Pregunta', 'content' => 'Respuesta', 'active' => true]]],
                'derafu-block-accordion',
            ],
            'alert' => [
                '<twig:block-alert content="Test" type="info" />',
                [],
                'derafu-block-alert',
            ],
            'box' => [
                '<twig:block-box title="Título" content="Contenido" />',
                [],
                'derafu-block-box',
            ],
            'boxes' => [
                '<twig:block-boxes :boxes="boxes" />',
                ['boxes' => [['icon' => 'fa-solid fa-star', 'title' => 'Título', 'content' => 'Texto']]],
                'derafu-block-boxes',
            ],
            'card' => [
                '<twig:block-card title="Título" content="Texto" />',
                [],
                'derafu-block-card',
            ],
            'card-grid' => [
                '<twig:block-card-grid :cards="cards" />',
                ['cards' => [['title' => 'Título', 'content' => 'Texto']]],
                'derafu-block-card-grid',
            ],
            'comparison' => [
                '<twig:block-comparison :plans="plans" />',
                ['plans' => [['title' => 'Básico', 'price' => '$10', 'features' => [], 'buttonText' => 'Elegir', 'buttonUrl' => '#']]],
                'derafu-block-comparison',
            ],
            'cta' => [
                '<twig:block-cta title="Título" content="Texto" buttonText="Click" buttonUrl="#" />',
                [],
                'derafu-block-cta',
            ],
            'features-grid' => [
                '<twig:block-features-grid :blocks="blocks" />',
                ['blocks' => [['title' => 'Bloque', 'subtitle' => 'Sub', 'features' => [['icon' => 'fa-solid fa-star', 'title' => 'F1', 'content' => 'C1']]]]],
                'derafu-block-features-grid',
            ],
            'features-icon' => [
                '<twig:block-features-icon :features="features" />',
                ['features' => [['icon' => 'fa-solid fa-star', 'title' => 'F1', 'content' => 'C1']]],
                'derafu-block-features-icon',
            ],
            'features-list' => [
                '<twig:block-features-list :featuresLeft="left" :featuresRight="right" />',
                [
                    'left' => [['icon' => 'fa-solid fa-star', 'title' => 'Izq', 'content' => 'C1']],
                    'right' => [['icon' => 'fa-solid fa-star', 'title' => 'Der', 'content' => 'C2']],
                ],
                'derafu-block-features-list',
            ],
            'features-table' => [
                '<twig:block-features-table :features="features" />',
                ['features' => [['title' => 'F1', 'values' => ['Plan A' => true, 'Plan B' => false]]]],
                'derafu-block-features-table',
            ],
            'footer' => [
                '<twig:block-footer col1Title="Empresa" leftText="© 2025" />',
                [],
                'derafu-block-footer',
            ],
            'grid' => [
                '<twig:block-grid :items="items" :cols="cols" />',
                ['items' => ['<p>Item 1</p>', '<p>Item 2</p>'], 'cols' => 2],
                'derafu-block-grid',
            ],
            'header' => [
                '<twig:block-header logoText="Logo" :leftNav="nav" />',
                ['nav' => [['type' => 'link', 'text' => 'Inicio', 'url' => '/', 'icon' => '']]],
                'derafu-block-header',
            ],
            'hero' => [
                '<twig:block-hero title="Bienvenido" subtitle="Subtítulo" />',
                [],
                'derafu-block-hero',
            ],
            'image' => [
                '<twig:block-image title="Imagen" image="/img/test.jpg" />',
                [],
                'derafu-block-image',
            ],
            'image-grid' => [
                '<twig:block-image-grid :images="images" />',
                ['images' => [['image' => '/img/test.jpg', 'url' => '#', 'tooltip' => 'Test']]],
                'derafu-block-image-grid',
            ],
            'media-list' => [
                '<twig:block-media-list :items="items" />',
                ['items' => [['image' => '/img/test.jpg', 'title' => 'Título', 'content' => 'Texto']]],
                'block-media-list',
            ],
            'modal' => [
                '<twig:block-modal title="Modal" content="Cuerpo" />',
                [],
                'modal',
            ],
            'offcanvas' => [
                '<twig:block-offcanvas title="Panel" content="Contenido" />',
                [],
                'offcanvas',
            ],
            'quote' => [
                '<twig:block-quote content="Una cita" author="Autor" />',
                [],
                'derafu-block-quote',
            ],
            'scrollspy' => [
                '<twig:block-scrollspy :sections="sections" />',
                ['sections' => [['id' => 's1', 'title' => 'Sección 1', 'content' => 'Texto 1']]],
                'derafu-block-scrollspy',
            ],
            'stats' => [
                '<twig:block-stats :stats="stats" />',
                ['stats' => [['value' => '100', 'text' => 'Usuarios']]],
                'derafu-block-stats',
            ],
            'steps' => [
                '<twig:block-steps :steps="steps" />',
                ['steps' => [['icon' => 'fa-solid fa-1', 'title' => 'Paso 1', 'content' => 'Descripción']]],
                'derafu-block-steps',
            ],
            'tabs' => [
                '<twig:block-tabs :tabs="tabs" />',
                ['tabs' => [['id' => 't1', 'title' => 'Tab 1', 'content' => '<p>Contenido</p>']]],
                'derafu-block-tabs',
            ],
            'team' => [
                '<twig:block-team :members="members" />',
                ['members' => [['image' => '/img/person.jpg', 'name' => 'Juan', 'role' => 'Dev', 'bio' => 'Bio', 'links' => []]]],
                'derafu-block-team',
            ],
            'testimonials' => [
                '<twig:block-testimonials :testimonials="testimonials" />',
                ['testimonials' => [['background' => '/img/bg.jpg', 'content' => 'Excelente', 'author' => 'María']]],
                'derafu-block-testimonials',
            ],
            'text-image' => [
                '<twig:block-text-image title="Título" content="Texto" image="/img/test.jpg" />',
                [],
                'derafu-block-text-image',
            ],
            'text-video' => [
                '<twig:block-text-video title="Título" content="Texto" video="https://www.youtube.com/embed/test" />',
                [],
                'derafu-block-text-video',
            ],
            'timeline' => [
                '<twig:block-timeline :events="events" />',
                ['events' => [['icon' => 'fa-solid fa-star', 'title' => 'Evento', 'content' => 'Desc', 'year' => 2024, 'date' => '2024-01-01']]],
                'derafu-block-timeline',
            ],
            'title' => [
                '<twig:block-title title="Título" subtitle="Sub" />',
                [],
                'derafu-block-title',
            ],
            'toast' => [
                '<twig:block-toast title="Aviso" content="Mensaje" type="primary" />',
                [],
                'derafu-block-toast',
            ],
            'video' => [
                '<twig:block-video title="Video" video="https://www.youtube.com/embed/test" />',
                [],
                'derafu-block-video',
            ],
            'video-grid' => [
                '<twig:block-video-grid :videos="videos" />',
                ['videos' => [['video' => 'https://www.youtube.com/embed/test', 'title' => 'Video 1', 'content' => 'Desc']]],
                'derafu-block-video-grid',
            ],
        ];
    }
}
