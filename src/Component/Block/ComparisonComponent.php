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

#[AsTwigComponent('block-comparison')]
class ComparisonComponent
{
    /**
     * Configuración
     *
     * Array de planes:
     *
     *   - title: título del plan
     *   - price: precio
     *   - period: período (/mes, /año, etc)
     *   - features: array de características:
     *     - text: texto de la característica
     *     - value: valor específico o true/false
     *     - tooltip: tooltip opcional
     *   - button_text: texto del botón
     *   - button_url: URL del botón
     *   - highlighted: true/false para destacar
     *
     * @var array
     */
    public array $plans = [];

    // Theme
    public string $theme = 'default';
}
