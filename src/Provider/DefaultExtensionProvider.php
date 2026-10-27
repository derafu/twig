<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Provider;

use Derafu\Twig\Contract\ExtensionProviderInterface;
use Derafu\Twig\Extension\MarkdownExtension;
use Derafu\Twig\Extension\TranslationExtension;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extra\CssInliner\CssInlinerExtension;
use Twig\Extra\Inky\InkyExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\Extra\String\StringExtension;

/**
 * Default extensions, after the extensions contributed by consumers.
 */
final class DefaultExtensionProvider implements ExtensionProviderInterface
{
    public function __construct(private readonly ?TranslatorInterface $translator = null)
    {
    }

    public function getExtensions(array $options = []): iterable
    {
        yield new TranslationExtension($this->translator);

        if (!($options['extra'] ?? true)) {
            return;
        }

        yield new MarkdownExtension();

        foreach ([CssInlinerExtension::class, IntlExtension::class, StringExtension::class, InkyExtension::class] as $class) {
            if (class_exists($class)) {
                yield new $class();
            }
        }
    }
}
