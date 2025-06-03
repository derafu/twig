<?php

declare(strict_types=1);

namespace Derafu\Twig\Component\Block;

use Derafu\Twig\Abstract\AbstractComponent;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent('block-features-grid')]
class FeaturesGridComponent extends AbstractComponent
{
    /**
     * Array of content blocks with their configurations.
     *
     * Each block contains:
     * - title: Block title
     * - subtitle: Block subtitle
     * - features: Array of features:
     *   - icon: Font Awesome icon class
     *   - title: Feature title
     *   - description: Feature description (supports HTML)
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $blocks = [];

    /**
     * Unique identifier for the features grid component.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $id = '';

    /**
     * Container class for the features grid component.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $container = null;

    /**
     * Additional CSS classes for the features grid component.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $class = null;
}
