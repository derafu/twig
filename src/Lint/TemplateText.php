<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Lint;

/**
 * A text written in a template that a person can read, and that does not go
 * through the translation: the text between the tags of the HTML, or the value
 * of an attribute that is shown (`alt`, `title`, `aria-label`...).
 *
 * Part of the lint tools: it is for tools and tests, never for the code that
 * runs the package.
 */
final readonly class TemplateText
{
    /**
     * @param string $text The text, without the HTML around it. What a template
     * puts in it (`{{ ... }}`) is not part of the text: it is only there to tell
     * that the text goes on.
     * @param string $kind Where the text is: `text` when it is between tags, or
     * the name of the attribute.
     * @param string $template Name of the template.
     * @param string $file The file of the template.
     * @param int $line Line of the template where the text starts.
     */
    public function __construct(
        public string $text,
        public string $kind,
        public string $template,
        public string $file,
        public int $line
    ) {
    }
}
