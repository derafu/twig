<?php

declare(strict_types=1);

namespace Derafu\Twig\Component\Block;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Table Component for displaying tabular data.
 *
 * This component creates a Bootstrap-based table that can be themed
 * and configured with different headers and row data.
 */
#[AsTwigComponent('block-table')]
class TableComponent
{
    /**
     * Unique identifier for the table component.
     *
     * @var string
     */
    public string $id;

    /**
     * Theme for styling the table component.
     *
     * @var string
     */
    public string $theme = 'default';

    /**
     * Use container wrapper.
     *
     * @var string
     */
    public string $container = 'container';

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
    public array $rows = [];

    /**
     * Visual style/type of the table (primary, secondary, etc).
     *
     * @var string
     */
    public string $type = '';

    /**
     * Constructor for the Table component.
     * Automatically generates a unique ID with 'table-' prefix.
     */
    public function __construct()
    {
        $this->id = uniqid('table-');
    }
}