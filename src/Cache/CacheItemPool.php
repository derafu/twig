<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Cache;

use Derafu\Twig\Support\ProjectDirectory;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

/**
 * Cache in files (PSR-6).
 *
 * By default the files are in `var/cache/<namespace>` of the project that is
 * running, the same place where `derafu/kernel` has its cache.
 */
class CacheItemPool extends PhpFilesAdapter implements CacheItemPoolInterface
{
    public function __construct(
        private string $namespace = 'derafu_twig',
        private int $defaultLifetime = 3600,
        private ?string $directory = null
    ) {
        $this->directory = $directory ?? ProjectDirectory::path() . '/var/cache';

        parent::__construct($this->namespace, $this->defaultLifetime, $this->directory);
    }
}
