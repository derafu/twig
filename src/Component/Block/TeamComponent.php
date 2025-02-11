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

#[AsTwigComponent('block-team')]
class TeamComponent
{
    // Layout
    public int $cols = 4;     // 1, 2, 3, 4, o 6 columnas

    // Alineación
    public string $align = 'center';  // left, center, right

    /**
     * Miembros del equipo
     *
     * Array de miembros con:
     *
     *  - image: URL de la imagen
     *  - name: nombre
     *  - role: rol/cargo
     *  - bio: biografía (opcional)
     *  - links: array de enlaces
     *    - icon: ícono (opcional)
     *    - text: texto
     *    - url: enlace
     *
     * @var array
     */
    public array $members = [];

    // Theme
    public string $theme = 'default';
}
