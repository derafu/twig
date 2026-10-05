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
use Derafu\Twig\Extension\RoutingExtension;
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

        $template = TemplateSource::load($this->twig, $name);

        $found = [];
        $this->collect($template->ast, $template, $functions, $found);

        $references = array_values($found);
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
        $templates = (new TemplateFinder())->find($directory, $extension);

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
     * @param array<string, RouteReference> $references
     */
    private function collect(Node $node, TemplateSource $template, array $functions, array &$references): void
    {
        if ($node instanceof FunctionExpression && in_array($node->getAttribute('name'), $functions, true)) {
            $parsedLine = $node->getTemplateLine();
            $name = $this->routeName($node);

            // Twig can have more than one node for what the template writes
            // once (it copies the node of a filter that it uses twice): it is
            // the same reference, in the same place.
            $references[$parsedLine . "\0" . $node->getAttribute('name') . "\0" . $name] ??= new RouteReference(
                $node->getAttribute('name'),
                $name,
                $template->name,
                $template->line($parsedLine)
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
