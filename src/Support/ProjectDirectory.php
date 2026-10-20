<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Support;

use Composer\InstalledVersions;

/**
 * The directory of the project that is running: the root package of Composer.
 *
 * It is how `derafu/kernel` finds it when the application does not say it.
 */
final class ProjectDirectory
{
    /**
     * The directory of the project, without `..` in it.
     */
    public static function path(): string
    {
        $installPath = InstalledVersions::getRootPackage()['install_path'];

        return realpath($installPath) ?: $installPath;
    }
}
