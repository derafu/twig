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
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('block-text-video')]
class TextVideoComponent extends AbstractComponent
{
    /**
     * Unique identifier for the text-image component.
     */
    #[ExposeInTemplate()]
    public string $id;

    /**
     * Additional CSS classes for the text-image component.
     */
    #[ExposeInTemplate()]
    public ?string $class = null;

    /**
     * Container wrapper class (e.g., 'container' or 'container-fluid').
     */
    #[ExposeInTemplate()]
    public ?string $container = null;

    /**
     * Position of the video (right, left).
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $video_position = 'right';

    /**
     * Number of columns for text section (Bootstrap grid).
     *
     * @var int
     */
    #[ExposeInTemplate()]
    public int $text_cols = 7;

    /**
     * Title of the text section.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $title;

    /**
     * Main content text (supports paragraphs).
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $content;

    /**
     * Array of button configurations.
     * Each button contains: {text, url}
     *
     * @var array
     */
    #[ExposeInTemplate()]
    public array $buttons = [];

    /**
     * YouTube video URL.
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $video_url;

    /**
     * Video aspect ratio (16:9, 4:3, 1:1).
     *
     * @var string
     */
    #[ExposeInTemplate()]
    public string $aspect_ratio = '16:9';

    /**
     * Show video player controls.
     *
     * @var bool
     */
    #[ExposeInTemplate()]
    public bool $show_controls = true;

    /**
     * Show video title.
     *
     * @var bool
     */
    #[ExposeInTemplate()]
    public bool $show_title = false;

    /**
     * Use privacy-enhanced mode (youtube-nocookie.com).
     *
     * @var bool
     */
    #[ExposeInTemplate()]
    public bool $enable_privacy = true;

    /**
     * Process component data before mount.
     * Converts YouTube URL to embed format and validates/generates ID.
     */
    #[PreMount]
    public function preMount(array $data): array
    {
        // Ensure ID is set, either provided or generated
        $data['id'] = $data['id'] ?? 'text-video-' . uniqid();

        // YouTube URL processing (existing logic)
        if (isset($data['video_url'])) {
            preg_match(
                '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/',
                $data['video_url'],
                $matches
            );
            $videoId = $matches[1] ?? '';

            $privacy = $data['enable_privacy'] ?? true;
            $base = $privacy
                ? 'https://www.youtube-nocookie.com/embed/'
                : 'https://www.youtube.com/embed/';

            $data['video_url'] = $base . $videoId;
        }

        return $data;
    }
}
