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

#[AsTwigComponent('block-footer')]
class FooterComponent extends AbstractComponent
{
    /**
     * Title for the first column.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $col1Title = null;

    /**
     * HTML content for the first column.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $col1Html = null;

    /**
     * Array of links for the first column.
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $col1Links = [];

    /**
     * Array of social icons for the first column.
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $col1SocialIcons = [];

    /**
     * Title for the second column.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $col2Title = null;

    /**
     * HTML content for the second column.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $col2Html = null;

    /**
     * Array of links for the second column.
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $col2Links = [];

    /**
     * Array of social icons for the second column.
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $col2SocialIcons = [];

    /**
     * Title for the third column.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $col3Title = null;

    /**
     * HTML content for the third column.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $col3Html = null;

    /**
     * Array of links for the third column.
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $col3Links = [];

    /**
     * Array of social icons for the third column.
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $col3SocialIcons = [];

    /**
     * Title for the fourth column.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $col4Title = null;

    /**
     * HTML content for the fourth column.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $col4Html = null;

    /**
     * Array of links for the fourth column.
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $col4Links = [];

    /**
     * Array of social icons for the fourth column.
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $col4SocialIcons = [];

    /**
     * Text content for the left side of footer bottom.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $leftText = null;

    /**
     * Text content for the right side of footer bottom.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $rightText = null;

    /**
     * Whether social icons should be displayed in circular style.
     *
     * @var bool
     */
    #[ExposeInTemplate()]
    public bool $socialIconsCircular = false;

    #[ExposeInTemplate()]
    public bool $fullWidth = false;
}
