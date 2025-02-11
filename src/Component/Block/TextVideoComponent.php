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
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('block-text-video')]
class TextVideoComponent
{
    // Layout

    public string $video_position = 'right';  // right, left

    public int $text_cols = 7;

    public int $video_cols = 5;

    // Contenido

    public string $title;

    public string $content;          // Texto que puede tener párrafos

    public array $buttons = [];      // Array de {text, url}

    // Video (YouTube)

    public string $video_url;        // URL de YouTube

    public string $aspect_ratio = '16:9';  // 16:9, 4:3, 1:1

    public bool $show_controls = true;

    public bool $show_title = false;

    public bool $enable_privacy = true;  // Usar youtube-nocookie.com

    // Visual
    public string $theme = 'default';

    #[PreMount]
    public function preMount(array $data): array
    {
        if (isset($data['video_url'])) {
            // Extrae el ID del video de diferentes formatos de URL de YouTube
            preg_match(
                '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/',
                $data['video_url'],
                $matches
            );
            $videoId = $matches[1] ?? '';

            // Base de la URL (normal o modo privacidad)
            $privacy = $data['enable_privacy'] ?? true;
            $base = $privacy
                ? 'https://www.youtube-nocookie.com/embed/'
                : 'https://www.youtube.com/embed/'
            ;

            // Actualiza la URL con la versión embed
            $data['video_url'] = $base . $videoId;
        }

        return $data;
    }
}
