<?php

declare(strict_types=1);

namespace Derafu\Twig\Component\Block;

use Derafu\Twig\Abstract\AbstractComponent;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent('block-features-icon')]
class FeaturesIconComponent extends AbstractComponent
{
    /**
     * Array of features with their configurations.
     *
     * Each feature contains:
     * - icon: Font Awesome icon class (e.g. "fa-solid fa-display")
     * - title: Feature title
     * - description: Feature description (supports HTML)
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $features = [];

    /**
     * Unique identifier for the features icon component.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $id = '';

    /**
     * Container class for the features icon component.
     * If null, defaults to 'container' when enabled.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $container = null;

    /**
     * Additional CSS classes for the features icon component.
     *
     * @var string|null
     */
    #[ExposeInTemplate()]
    public ?string $class = null;
}
