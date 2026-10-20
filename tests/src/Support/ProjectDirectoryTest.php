<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Support;

use Composer\InstalledVersions;
use Derafu\Twig\Support\ProjectDirectory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * The directory of the project is the one of the root package of Composer.
 */
#[CoversClass(ProjectDirectory::class)]
final class ProjectDirectoryTest extends TestCase
{
    public function testItIsTheDirectoryOfTheRootPackage(): void
    {
        $path = ProjectDirectory::path();

        $this->assertDirectoryExists($path);
        $this->assertSame(realpath(InstalledVersions::getRootPackage()['install_path']), $path);
        $this->assertSame(dirname(__DIR__, 3), $path);
        $this->assertStringNotContainsString('..', $path);
    }
}
