<?php

declare(strict_types=1);

namespace Derafu\Twig\Component\Block;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('block-card')]
class CardComponent
{
    /**
     * Unique identifier for the card component.
     *
     * @var string
     */
    public string $id;

    /**
     * Theme for styling the card component.
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
     * Array of cards to be displayed.
     *
     * Optional variables for each card:
     * - image: Optional card image URL
     * - title: Optional card title
     * - description: Optional card description
     * - button_text: Optional button text
     * - button_url: Optional button URL (required if button_text is present)
     *
     * @var array{
     *     image?: ?string,
     *     title?: ?string,
     *     description?: ?string,
     *     button_text?: ?string,
     *     button_url?: ?string
     * }[]
     */
    public array $cards = [];

    /**
     * Constructor for the Card component.
     * Automatically generates a unique ID with 'card-' prefix.
     */
    public function __construct()
    {
        $this->id = uniqid('card-');
    }
}