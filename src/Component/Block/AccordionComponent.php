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

#[AsTwigComponent('block-accordion')]
class AccordionComponent
{
    // Configuración
    public array $items = [];    // Array de {title, content, active}

    // Identificador único para el acordeón
    public string $id;

    // Theme
    public string $theme = 'default';

    public function __construct()
    {
        // Generar ID único si no se proporciona
        $this->id = uniqid('accordion-');
    }
}
