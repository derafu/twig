<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.org>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Component\Block;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('block-testimonials')]
class TestimonialsComponent
{
    // Identificador único para los testimonios.
    public string $id;

    /**
     * Testimonials array.
     *
     * Ejemplo:
     * 'image' => 'url/imagen.jpg',
     * 'quote' => 'Texto del testimonio...',
     * 'author' => 'Nombre Autor',
     * 'company' => 'Empresa', // opcional
     * 'url' => 'https://...', // opcional, para links en el texto
     * 'position' => 'center', // Card position (center, left, right).
     *
     * @var array
     */
    public array $testimonials = [];

    // Carousel options.

    public bool $autoplay = true;

    public int $interval = 5000; // milliseconds

    public bool $showControls = true;

    public bool $showIndicators = true;

    // Style options.

    public int $maxWidth = 600; // pixels

    public int $brightness = 70; // percentage

    public int $height = 500; // pixels

    // Theme.
    public string $theme = 'default';

    // Recommended image dimensions (informative only).

    public const RECOMMENDED_IMAGE_WIDTH = 1920;

    public const RECOMMENDED_IMAGE_HEIGHT = 500;

    public function __construct()
    {
        // Generar ID único si no se proporciona
        $this->id = uniqid('testimonials-');
    }
}
