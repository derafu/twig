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
 * This component creates a Bootstrap-based table that can be themed and
 * configured with different headers and row data.
 */
#[AsTwigComponent('block-table')]
class TableComponent extends AbstractComponent
{
    /**
     * Unique identifier for the table component.
     */
    #[ExposeInTemplate()]
    public string $id;

    /**
     * Additional CSS classes for the table component.
     */
    #[ExposeInTemplate()]
    public ?string $class = null;

    /**
     * Container wrapper class (e.g., 'container' or 'container-fluid').
     */
    #[ExposeInTemplate()]
    public ?string $container = null;

    /**
     * Table header configuration.
     *
     * Structure per cell:
     *
     *   - text: Cell content (supports HTML).
     *   - scope: Accessibility scope (col, colgroup).
     *   - colspan: Number of columns to span.
     *   - rowspan: Number of rows to span.
     *   - width: Fixed width in pixels or percentage.
     *   - align: Text alignment (left, center, right).
     *   - class: Additional CSS classes.
     *
     * @var array[][] Array of rows, each containing array of cells.
     */
    #[ExposeInTemplate()]
    public array $headers = [];

    /**
     * Table rows data.
     *
     * First key is row index, second is column index.
     *
     * @var array[][]
     */
    #[ExposeInTemplate()]
    public array $rows = [];

    /**
     * Footer rows configuration (same structure as headers).
     *
     * @var array[][]
     */
    #[ExposeInTemplate()]
    public array $footer = [];

    /**
     * Actions menu items.
     * Structure:
     *
     *   - text: Link/button text.
     *   - icon: Optional Font Awesome icon class.
     *   - url: Optional URL for link.
     *   - onclick: Optional JavaScript action.
     *   - type: Optional 'divider' type.
     *
     * @var array[]
     */
    #[ExposeInTemplate()]
    public array $actions = [];

    /**
     * Column configurations.
     *
     * Key is column index.
     *
     * Structure:
     *
     *   - width: Width in pixels or percentage.
     *   - align: Text alignment (left, center, right).
     *   - class: Additional CSS classes.
     *
     * @var array<int, array{
     *     width?: string,
     *     align?: string,
     *     class?: string
     * }>
     */
    #[ExposeInTemplate()]
    public array $columns = [];

    /**
     * Row classes based on column values
     *
     * Structure: [column_index => [value => class_name]]
     *
     * @var array<int, array<string, string>>
     */
    #[ExposeInTemplate()]
    public array $rowClasses = [];

    /**
     * Table caption (optional).
     */
    #[ExposeInTemplate()]
    public ?string $caption = null;

    /**
     * Maximum height for table container in pixels.
     */
    #[ExposeInTemplate()]
    public ?int $maxHeight = null;

    /**
     * Show/hide toggle buttons.
     */
    #[ExposeInTemplate()]
    public bool $toggleable = false;

    /**
     * Initial visibility state if toggleable.
     */
    #[ExposeInTemplate()]
    public bool $visible = true;

    /**
     * Hide empty columns.
     */
    #[ExposeInTemplate()]
    public bool $hideEmptyCols = false;

    #[ExposeInTemplate()]
    public bool $striped = false;

    #[ExposeInTemplate()]
    public bool $bordered = true;

    #[ExposeInTemplate()]
    public bool $hover = true;

    #[ExposeInTemplate()]
    public bool $small = false;
}
