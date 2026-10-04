<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Translation;

use Derafu\Translation\Contract\TranslationResourceProviderInterface;

/**
 * Provides the translations of this package: the messages of its exceptions.
 */
final class TwigTranslationResourceProvider implements TranslationResourceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getDirectories(): iterable
    {
        return [__DIR__ . '/../../resources/translations'];
    }
}
