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

use Twig\Environment;
use Twig\Node\ModuleNode;

/**
 * A template as the lint tools read it: what Twig parses, with the lines of the
 * file that it comes from.
 *
 * Twig does not parse the code of the file: the environment can change it
 * before (the `<twig:...>` tags of the components are written again as tags of
 * Twig, and a tag of several lines becomes one line), and then the lines of the
 * nodes are the lines of that code and not of the file. This knows both, so a
 * finding says the line of the file.
 *
 * Part of the lint tools: it is for tools and tests, never for the code that
 * runs the package.
 */
final class TemplateSource
{
    /**
     * The most that is aligned: a template with more lines than this keeps the
     * lines that Twig gives, so reading it does not take a lot of memory.
     */
    private const MAX_ALIGNMENT = 4_000_000;

    /**
     * @var list<string> The lines of the code that Twig parsed.
     */
    private array $parsedLines;

    /**
     * @var array<int, int>|null The line of the file of each line of the code
     * that Twig parsed, or `null` if they are the same.
     */
    private ?array $fileLines;

    /**
     * @param string $name Name of the template, as the loader knows it.
     * @param string $file The file of the template (its name, if it has none).
     * @param ModuleNode $ast What Twig parsed.
     */
    private function __construct(
        public readonly string $name,
        public readonly string $file,
        public readonly ModuleNode $ast,
        string $parsedCode,
        string $fileCode
    ) {
        $this->parsedLines = explode("\n", $this->normalize($parsedCode));
        $this->fileLines = $this->align(explode("\n", $this->normalize($fileCode)));
    }

    /**
     * Loads and parses a template with the environment.
     *
     * @param Environment $twig The environment the template is written for.
     * @param string $name Name of the template, as the loader knows it.
     * @throws \Twig\Error\Error If the template can not be loaded or parsed with
     * this environment. It is not hidden: a template that can not be read would
     * look like one with nothing in it.
     */
    public static function load(Environment $twig, string $name): self
    {
        $source = $twig->getLoader()->getSourceContext($name);
        $stream = $twig->tokenize($source);
        $ast = $twig->parse($stream);

        return new self(
            $name,
            $source->getPath() !== '' ? $source->getPath() : $name,
            $ast,
            $stream->getSourceContext()->getCode(),
            $source->getCode()
        );
    }

    /**
     * The line of the file for a line of the nodes of Twig. When the code was
     * written again, a line inside a tag of several lines is the line where the
     * tag starts.
     */
    public function line(int $line): int
    {
        return $this->fileLines[$line] ?? $line;
    }

    /**
     * The text of a line of the nodes of Twig, as Twig parsed it, without the
     * blanks around it.
     */
    public function text(int $line): string
    {
        return trim($this->parsedLines[$line - 1] ?? '');
    }

    private function normalize(string $code): string
    {
        return str_replace(["\r\n", "\r"], "\n", $code);
    }

    /**
     * Matches the lines that Twig parsed with the lines of the file: the lines
     * that were not written again are the same in both, and the ones that were
     * are between them.
     *
     * @param list<string> $fileLines
     * @return array<int, int>|null
     */
    private function align(array $fileLines): ?array
    {
        $parsed = array_map('trim', $this->parsedLines);
        $file = array_map('trim', $fileLines);

        if ($parsed === $file) {
            return null;
        }

        $n = count($parsed);
        $m = count($file);
        if ($n * $m > self::MAX_ALIGNMENT) {
            return null;
        }

        // Longest common subsequence of lines, from the end.
        $table = array_fill(0, $n + 1, array_fill(0, $m + 1, 0));
        for ($i = $n - 1; $i >= 0; --$i) {
            for ($j = $m - 1; $j >= 0; --$j) {
                $table[$i][$j] = $parsed[$i] === $file[$j]
                    ? $table[$i + 1][$j + 1] + 1
                    : max($table[$i + 1][$j], $table[$i][$j + 1]);
            }
        }

        // A line that matches is the same line of the file. One that does not
        // was written again: it is the line where the lines that were joined
        // start, that is, the line after the last one that matched.
        $lines = [];
        $next = 1;
        $i = $j = 0;
        while ($i < $n) {
            if ($j < $m && $parsed[$i] === $file[$j] && $table[$i][$j] === $table[$i + 1][$j + 1] + 1) {
                $lines[$i + 1] = $j + 1;
                $next = $j + 2;
                ++$i;
                ++$j;
            } elseif ($j < $m && $table[$i][$j + 1] >= $table[$i + 1][$j]) {
                ++$j;
            } else {
                $lines[$i + 1] = $next;
                ++$i;
            }
        }

        return $lines;
    }
}
