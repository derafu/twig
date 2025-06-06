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

#[AsTwigComponent('block-timeline')]
class TimelineComponent extends AbstractComponent
{
    /**
     * Array of timeline events configurations.
     *
     * Each event contains:
     * - date: Event date (YYYY-MM-DD, YYYYMM, YYYY).
     * - title: Event title (optional).
     * - content: Event content (required).
     * - icon: Font Awesome icon class (e.g. "fa-solid fa-check-circle fa-fw").
     *
     * @var array
     */
    private array $events = [];

    /**
     * Position of the timeline line (center, left, right).
     *
     * @var string
     */
    private string $linePosition = 'center';

    /**
     * Optional date format for event dates.
     *
     * @var string|null
     */
    private ?string $dateFormat = null;

    /**
     * Gets the array of timeline events.
     *
     * @return array The array of timeline events.
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * Sets the array of timeline events.
     *
     * @param array $events The array of timeline events.
     * @return static
     */
    public function setEvents(array $events): static
    {
        $this->events = $events;

        return $this;
    }

    /**
     * Gets the position of the timeline line.
     *
     * @return string The line position.
     */
    public function getLinePosition(): string
    {
        return $this->linePosition;
    }

    /**
     * Sets the position of the timeline line.
     *
     * @param string $linePosition The line position.
     * @return static
     */
    public function setLinePosition(string $linePosition): static
    {
        $this->linePosition = $linePosition;

        return $this;
    }

    /**
     * Gets the date format for event dates.
     *
     * @return string|null The date format.
     */
    public function getDateFormat(): ?string
    {
        return $this->dateFormat;
    }

    /**
     * Sets the date format for event dates.
     *
     * @param string|null $dateFormat The date format.
     * @return static
     */
    public function setDateFormat(?string $dateFormat): static
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }
}
