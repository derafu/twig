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

use Derafu\Twig\Abstract\AbstractComponent;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

/**
 * Table Component for displaying tabular data.
 *
 * This component creates a Bootstrap-based table that can be themed
 * and configured with different headers and row data.
 */
#[AsTwigComponent('block-table')]
class TableComponent extends AbstractComponent
{
    /**
     * Array of table headers configurations.
     *
     * Optional variables for headers:
     * - text: Optional header text (supports HTML)
     * - scope: Optional header scope (col, row)
     *
     * @var array{
     *     text?: ?string,
     *     scope?: ?string
     * }[]
     */
    #[ExposeInTemplate()]
    public array $headers = [];

    /**
     * Array of table rows configurations.
     *
     * Optional variables for rows:
     * - id: Optional row identifier
     *   {
     *     text: Identifier text,
     *     scope: Identifier scope
     *   }
     * - nombre: Optional name
     * - empresa: Optional company name
     * - correo: Optional email address
     * - estado: Optional status
     *
     * @var array{
     *     id?: ?array{text: string, scope?: string},
     *     nombre?: ?string,
     *     empresa?: ?string,
     *     correo?: ?string,
     *     estado?: ?string
     * }[]
     */
    #[ExposeInTemplate()]
    public array $rows = [];
}
