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
    /**
     * Unique identifier for the accordion component.
     *
     * @var string
     */
    public string $id;

    /**
     * Theme for styling the accordion component.
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
     * Array of accordion items.
     * Each item should contain: {title, content, active}
     *
     * @var array
     */
    public array $items = [];

    /**
     * Constructor for the Accordion component.
     * Automatically generates a unique ID with 'accordion-' prefix.
     */
    public function __construct()
    {
        $this->id = uniqid('accordion-');
    }
}
