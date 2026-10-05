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
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;
use Twig\Node\PrintNode;
use Twig\Node\TextNode;

/**
 * Finds the texts that the templates write and that a person can read, so they
 * can be checked to go through the translation: the text between the tags of the
 * HTML and the value of the attributes that are shown to people.
 *
 * It reads what Twig itself parses, with the environment the templates are
 * written for, and then reads the HTML that the templates write, joining the
 * pieces of text that Twig splits around what the template prints and its tags.
 * What a template prints (`{{ ... }}`) is not a text of the template, but it is
 * known to be there: `Slide {{ loop.index }}` is the text `Slide`.
 *
 * A text is the letters of any alphabet; punctuation, numbers and symbols are
 * not a text that is translated. The content of `<script>` and `<style>` and the
 * comments of the HTML are not read: they are not shown.
 *
 * It only finds texts. Whether one of them has to be translated (a brand name,
 * for example, does not) is for whoever uses it to decide.
 *
 * Part of the lint tools: it is for tools and tests, never for the code that
 * runs the package.
 */
final class TemplateTextScanner
{
    /**
     * The attributes whose value a person reads.
     */
    public const ATTRIBUTES = [
        'alt',
        'title',
        'aria-label',
        'aria-description',
        'aria-placeholder',
        'placeholder',
        'label',
        'data-bs-title',
        'data-bs-content',
    ];

    /**
     * What stands for what a template prints, while the HTML is read.
     */
    private const HOLE = "\x00";

    /**
     * @param Environment $twig The environment the templates are written for.
     * @param list<string> $attributes The attributes whose value a person reads.
     */
    public function __construct(
        private readonly Environment $twig,
        private readonly array $attributes = self::ATTRIBUTES
    ) {
    }

    /**
     * Finds the texts of a template, in order of line.
     *
     * @param string $name Name of the template, as the loader knows it.
     * @return list<TemplateText>
     * @throws \Twig\Error\Error If the template can not be loaded or parsed with
     * this environment. It is not hidden: a template that can not be read would
     * look like one with no texts.
     */
    public function scanTemplate(string $name): array
    {
        $source = $this->twig->getLoader()->getSourceContext($name);
        $ast = $this->twig->parse($this->twig->tokenize($source));

        $html = '';
        $lines = [];
        $this->flatten($ast, $html, $lines);

        $file = $source->getPath() !== '' ? $source->getPath() : $name;

        $texts = [];
        foreach ($this->read($html) as [$text, $kind, $offset]) {
            $texts[] = new TemplateText($text, $kind, $name, $file, $lines[$offset] ?? 1);
        }

        usort($texts, fn (TemplateText $a, TemplateText $b) => $a->line <=> $b->line);

        return $texts;
    }

    /**
     * Finds the texts of every template of a directory, in order of template and
     * then of line.
     *
     * The directory must be one of the paths of the loader of the environment,
     * because the templates are loaded by their name relative to it.
     *
     * @param string $directory Directory with the templates.
     * @param string $extension Extension of the files that are templates.
     * @return list<TemplateText>
     */
    public function scanDirectory(string $directory, string $extension = 'twig'): array
    {
        $texts = [];
        foreach ((new TemplateFinder())->find($directory, $extension) as $template) {
            array_push($texts, ...$this->scanTemplate($template));
        }

        return $texts;
    }

    /**
     * Joins the text of the template in the order it is written. What a
     * template prints, and every tag of Twig, stands for a hole, so the text
     * before and after it does not run together. `$lines` has the line of each
     * position of the HTML.
     *
     * @param array<int, int> $lines
     */
    private function flatten(Node $node, string &$html, array &$lines): void
    {
        if ($node instanceof TextNode) {
            $line = $node->getTemplateLine();
            foreach (str_split($node->getAttribute('data')) as $character) {
                $lines[strlen($html)] = $line;
                $html .= $character;
                if ($character === "\n") {
                    ++$line;
                }
            }

            return;
        }

        if ($node instanceof AbstractExpression) {
            return;
        }

        $this->hole($html, $lines, $node->getTemplateLine());

        if (!$node instanceof PrintNode) {
            foreach ($node as $child) {
                $this->flatten($child, $html, $lines);
                if (!$child instanceof TextNode) {
                    $this->hole($html, $lines, $child->getTemplateLine());
                }
            }
        }
    }

    /**
     * @param array<int, int> $lines
     */
    private function hole(string &$html, array &$lines, int $line): void
    {
        if (!str_ends_with($html, self::HOLE)) {
            $lines[strlen($html)] = $line;
            $html .= self::HOLE;
        }
    }

    /**
     * Reads the HTML and gives the texts that a person reads: what is between
     * the tags, and the values of the attributes that are shown.
     *
     * @return list<array{string, string, int}> The text, where it is, and the
     * position of the HTML where it starts.
     */
    private function read(string $html): array
    {
        $found = [];
        $start = 0;
        $position = 0;
        $length = strlen($html);

        while (true) {
            $tag = strpos($html, '<', $position);

            if ($tag === false) {
                $this->add($found, substr($html, $start), 'text', $start);

                break;
            }

            if (str_starts_with(substr($html, $tag, 4), '<!--')) {
                $this->add($found, substr($html, $start, $tag - $start), 'text', $start);
                $close = strpos($html, '-->', $tag);
                $position = $start = $close === false ? $length : $close + 3;

                continue;
            }

            if (!preg_match('/\G<(\/?)([A-Za-z][A-Za-z0-9:-]*)((?:[^>"\']|"[^"]*"|\'[^\']*\')*)>?/', $html, $match, PREG_OFFSET_CAPTURE, $tag)) {
                // A "<" that does not start a tag is part of the text.
                $position = $tag + 1;

                continue;
            }

            $this->add($found, substr($html, $start, $tag - $start), 'text', $start);

            $position = $tag + strlen($match[0][0]);

            if ($match[1][0] === '') {
                $this->attributes($found, $match[3][0], $match[3][1]);

                $name = strtolower($match[2][0]);
                if (in_array($name, ['script', 'style'], true)) {
                    $close = stripos($html, '</' . $name, $position);
                    $position = $close === false ? $length : $close;
                }
            }

            $start = $position;
        }

        return $found;
    }

    /**
     * @param list<array{string, string, int}> $found
     */
    private function attributes(array &$found, string $attributes, int $offset): void
    {
        preg_match_all('/([^\s"\'=\/<>\x00]+)\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/', $attributes, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        foreach ($matches as $match) {
            $name = strtolower($match[1][0]);
            if (!in_array($name, $this->attributes, true)) {
                continue;
            }

            $value = isset($match[3]) && $match[3][1] >= 0 ? $match[3] : $match[2];
            $this->add($found, $value[0], $name, $offset + $value[1]);
        }
    }

    /**
     * Adds a text, if it has letters.
     *
     * @param list<array{string, string, int}> $found
     */
    private function add(array &$found, string $raw, string $kind, int $offset): void
    {
        $text = trim((string) preg_replace('/\s+/u', ' ', str_replace(self::HOLE, ' ', html_entity_decode($raw, ENT_QUOTES | ENT_HTML5))));

        if ($text === '' || preg_match('/\p{L}/u', $text) !== 1) {
            return;
        }

        // The line is where the text starts, not the blanks before it.
        $found[] = [$text, $kind, $offset + (strlen($raw) - strlen(ltrim($raw, " \t\r\n" . self::HOLE)))];
    }
}
