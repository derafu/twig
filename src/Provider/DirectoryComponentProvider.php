<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Provider;

use Derafu\Translation\Exception\Core\TranslatableRuntimeException as RuntimeException;
use Derafu\Translation\Exception\Logic\TranslatableInvalidArgumentException as InvalidArgumentException;
use Derafu\Twig\Cache\CacheItemPool;
use Derafu\Twig\Contract\ComponentProviderInterface;
use Psr\Cache\CacheItemPoolInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

class DirectoryComponentProvider implements ComponentProviderInterface
{
    private readonly CacheItemPoolInterface $cache;

    public function __construct(
        private readonly string $directory,
        ?CacheItemPoolInterface $cache = null,
        private string $cacheKey = 'derafu_twig_components_dir_%s'
    ) {
        if (!is_dir($directory)) {
            throw new InvalidArgumentException([
                'Directory "{directory}" does not exist.',
                'directory' => $directory,
            ]);
        }
        $this->cache = $cache ?? new CacheItemPool();
        $this->cacheKey = sprintf($this->cacheKey, hash('sha256', $directory));
    }

    /**
     * {@inheritDoc}
     */
    public function getComponents(): array
    {
        // Try to get from cache.
        $item = $this->cache->getItem($this->cacheKey);
        if ($item->isHit() && is_array($cached = $item->get())) {
            $components = [];
            foreach ($cached as $class) {
                if (is_string($class) && class_exists($class)) {
                    $components[] = $class;
                }
            }

            return $components;
        }

        // If not in cache, scan and store.
        $components = $this->scanDirectory();
        $item->set($components);
        $this->cache->save($item);

        return $components;
    }

    /**
     * @return list<class-string>
     */
    private function scanDirectory(): array
    {
        $components = [];
        $files = $this->findPhpFiles();

        foreach ($files as $file) {
            $class = $this->getClassFromFile($file);
            if ($class && $this->isTwigComponent($class)) {
                $components[] = $class;
            }
        }

        return $components;
    }

    /**
     * @return list<string>
     */
    private function findPhpFiles(): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->directory)
        );

        foreach ($iterator as $file) {
            if ($file instanceof SplFileInfo && $file->isFile() && $file->getExtension() === 'php') {
                $realPath = $file->getRealPath();
                if ($realPath !== false) {
                    $files[] = $realPath;
                }
            }
        }

        return $files;
    }

    /**
     * @return class-string|null
     * @throws RuntimeException If the file can not be read.
     */
    private function getClassFromFile(string $file): ?string
    {
        $content = is_readable($file) ? file_get_contents($file) : false;
        if ($content === false) {
            throw new RuntimeException([
                'The file {file} can not be read.',
                'file' => $file,
            ]);
        }

        // Get namespace.
        $namespace = '';
        if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
            $namespace = $matches[1];
        }

        // Get class name.
        if (preg_match('/class\s+(\w+)/', $content, $matches)) {
            $className = $matches[1];
            $fullClassName = $namespace ? $namespace . '\\' . $className : $className;

            // Check that the class exists and can be loaded.
            if (class_exists($fullClassName)) {
                return $fullClassName;
            }
        }

        return null;
    }

    /**
     * @param class-string $class
     */
    private function isTwigComponent(string $class): bool
    {
        return !empty((new ReflectionClass($class))->getAttributes(AsTwigComponent::class));
    }
}
