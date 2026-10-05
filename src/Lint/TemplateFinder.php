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

use Derafu\Translation\Exception\Logic\TranslatableInvalidArgumentException as InvalidArgumentException;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Finds the templates of a directory, by their name relative to it, as the
 * loader of a Twig environment knows them.
 *
 * Part of the lint tools: it is for tools and tests, never for the code that
 * runs the package.
 */
final class TemplateFinder
{
    /**
     * @param string $directory Directory with the templates.
     * @param string $extension Extension of the files that are templates.
     * @return list<string> Names of the templates, in order.
     * @throws InvalidArgumentException If the directory does not exist.
     */
    public function find(string $directory, string $extension = 'twig'): array
    {
        if (!is_dir($directory)) {
            throw new InvalidArgumentException([
                'The directory {directory} does not exist.',
                'directory' => $directory,
            ]);
        }

        $root = rtrim(realpath($directory) ?: $directory, '/');

        $templates = [];
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === $extension) {
                $templates[] = substr($file->getPathname(), strlen($root) + 1);
            }
        }
        sort($templates);

        return $templates;
    }
}
