<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Extension;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Twig extension to enable Twig rendering within templates.
 *
 * This extension provides a `twig` filter to convert Twig content in a string
 * into HTML using Twig.
 */
class TwigExtension extends AbstractExtension
{
    /**
     * @return array<int,TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('twig', [$this, 'renderTwig'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
                'needs_context' => true,
            ]),
        ];
    }

    /**
     * Renders a string as a Twig template, with the context of the caller.
     *
     * @param Environment $twig The Twig environment.
     * @param array<string,mixed> $context Context to pass to the Twig template.
     * @param string $templateString The Twig template.
     * @return string The rendered template.
     */
    public function renderTwig(
        Environment $twig,
        array $context,
        string $templateString
    ): string {
        $template = $twig->createTemplate($templateString);

        return $template->render($context);
    }
}
