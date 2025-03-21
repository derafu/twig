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

#[AsTwigComponent('block-box')]
class BoxComponent extends AbstractComponent
{
    /**
     * Title of the box component.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $title;

    /**
     * Content text of the box (supports HTML).
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $content = null;

    /**
     * Rounded border of the box.
     *
     * @var bool
     */
    #[ExposeInTemplate()]
    public bool $rounded = true;
}
