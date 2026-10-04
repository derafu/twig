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

use Derafu\Translation\Exception\Core\TranslatableLogicException as LogicException;
use Derafu\Translation\Exception\Logic\TranslatableInvalidArgumentException as InvalidArgumentException;
use Derafu\Twig\Extension\RoutingExtension;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Twig\Environment;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\FunctionExpression;
use Twig\Node\Node;

/**
 * Finds the routes that the templates refer to, in their calls to `path()`,
 * `url()` and `is_active_path()`.
 *
 * It reads what Twig itself parses, with the environment the templates are
 * written for (its functions, tags and components), so a comment, a text or a
 * string that only looks like a call is never taken for one. Nothing is
 * rendered.
 *
 * It only finds references. Whether a route exists is for whoever uses it to
 * decide, asking the router of the templates that were scanned: that is a fact
 * about which templates belong to which routes, which only the user knows.
 *
 * Part of the lint tools: it is for tools and tests, never for the code that
 * runs the package.
 */
final class RouteReferenceScanner
{
    public function __construct(private readonly Environment $twig)
    {
    }

    /**
     * Finds the references to routes of a template, in order of line.
     *
     * @param string $name Name of the template, as the loader knows it.
     * @return list<RouteReference>
     * @throws LogicException If the environment has no routing functions.
     * @throws \Twig\Error\Error If the template can not be loaded or parsed
     * with this environment. It is not hidden: a template that can not be read
     * would look like one with no references.
     */
    public function scanTemplate(string $name): array
    {
        $functions = $this->routingFunctions();

        $source = $this->twig->getLoader()->getSourceContext($name);
        $ast = $this->twig->parse($this->twig->tokenize($source));

        $references = [];
        $this->collect($ast, $name, $functions, $references);

        usort($references, fn (RouteReference $a, RouteReference $b) => $a->line <=> $b->line);

        return $references;
    }

    /**
     * Finds the references to routes of every template of a directory, in order
     * of template and then of line.
     *
     * The directory must be one of the paths of the loader of the environment,
     * because the templates are loaded by their name relative to it.
     *
     * @param string $directory Directory with the templates.
     * @param string $extension Extension of the files that are templates.
     * @return list<RouteReference>
     */
    public function scanDirectory(string $directory, string $extension = 'twig'): array
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

        $references = [];
        foreach ($templates as $template) {
            array_push($references, ...$this->scanTemplate($template));
        }

        return $references;
    }

    /**
     * Names of the functions of the environment that are routing functions.
     *
     * They are recognized by what they are (they belong to the routing
     * extension) and not by their name: a `path()` of another extension is not
     * a route. Every function of that extension takes the name of the route as
     * its first argument.
     *
     * @return list<string>
     */
    private function routingFunctions(): array
    {
        $names = [];
        foreach ($this->twig->getFunctions() as $name => $function) {
            $callable = $function->getCallable();

            if (is_array($callable) && $callable[0] instanceof RoutingExtension) {
                $names[] = $name;
            }
        }

        if ($names === []) {
            throw new LogicException([
                'The Twig environment has no routing functions: register the {extension}.',
                'extension' => RoutingExtension::class,
            ]);
        }

        return $names;
    }

    /**
     * @param list<string> $functions
     * @param list<RouteReference> $references
     */
    private function collect(Node $node, string $template, array $functions, array &$references): void
    {
        if ($node instanceof FunctionExpression && in_array($node->getAttribute('name'), $functions, true)) {
            $references[] = new RouteReference(
                $node->getAttribute('name'),
                $this->routeName($node),
                $template,
                $node->getTemplateLine()
            );
        }

        foreach ($node as $child) {
            $this->collect($child, $template, $functions, $references);
        }
    }

    /**
     * The name of the route of a call, if it is written as a literal.
     */
    private function routeName(FunctionExpression $call): ?string
    {
        $arguments = $call->getNode('arguments');

        $first = match (true) {
            $arguments->hasNode('0') => $arguments->getNode('0'),
            $arguments->hasNode('name') => $arguments->getNode('name'),
            default => null,
        };

        if ($first instanceof ConstantExpression && is_string($first->getAttribute('value'))) {
            return $first->getAttribute('value');
        }

        return null;
    }
}
