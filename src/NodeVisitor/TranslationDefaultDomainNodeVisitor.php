<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\NodeVisitor;

use Derafu\Twig\Node\TransDefaultDomainNode;
use Derafu\Twig\Node\TransNode;
use Twig\Environment;
use Twig\Node\BlockNode;
use Twig\Node\EmptyNode;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\FilterExpression;
use Twig\Node\Expression\Variable\AssignContextVariable;
use Twig\Node\Expression\Variable\ContextVariable;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\Node\Nodes;
use Twig\Node\SetNode;
use Twig\NodeVisitor\NodeVisitorInterface;

/**
 * Implements the `{% trans_default_domain %}` tag: sets a default domain for
 * every `trans` filter call and `{% trans %}` block that follows it in the
 * same template or block and does not have its own domain explicitly.
 *
 * Deliberately simplified against Symfony's own implementation of the same
 * tag: it only targets the current Twig node/expression API, with no dual
 * code path for older Twig internals.
 *
 * A dynamic (non-literal) domain expression, e.g.
 * `{% trans_default_domain some_var %}`, is fully supported: the expression
 * is evaluated once into a synthetic template variable, which every affected
 * `trans` call then references. A library extension must not assume a fixed
 * domain for its consumers, so restricting this tag to literal strings only
 * was not an acceptable simplification.
 */
final class TranslationDefaultDomainNodeVisitor implements NodeVisitorInterface
{
    /**
     * Stack of the active default domain expression per nesting level
     * (template/block). `null` means no default domain is active at that
     * level.
     *
     * @var array<int, AbstractExpression|null>
     */
    private array $domainStack = [null];

    /**
     * {@inheritDoc}
     */
    public function enterNode(Node $node, Environment $env): Node
    {
        if ($node instanceof BlockNode || $node instanceof ModuleNode) {
            // Inherit the domain active in the enclosing scope.
            $this->domainStack[] = end($this->domainStack);
        }

        if ($node instanceof TransDefaultDomainNode) {
            return $this->replaceWithDomainAssignment($node);
        }

        $domain = end($this->domainStack);

        if ($domain === null) {
            return $node;
        }

        if ($node instanceof FilterExpression && 'trans' === $node->getAttribute('name')) {
            $this->applyDefaultDomain($node, $domain);
        } elseif ($node instanceof TransNode && !$node->hasNode('domain')) {
            $node->setNode('domain', $domain);
        }

        return $node;
    }

    /**
     * {@inheritDoc}
     */
    public function leaveNode(Node $node, Environment $env): ?Node
    {
        if ($node instanceof TransDefaultDomainNode) {
            // Remove the marker node from the compiled tree (only reached
            // for a literal domain, which does not need a SetNode).
            return null;
        }

        if ($node instanceof BlockNode || $node instanceof ModuleNode) {
            array_pop($this->domainStack);
        }

        return $node;
    }

    /**
     * {@inheritDoc}
     */
    public function getPriority(): int
    {
        return -10;
    }

    /**
     * Records the domain expression carried by a `trans_default_domain`
     * marker node into the current scope.
     *
     * For a literal string, the expression itself is reused directly in
     * every affected `trans` call. For a dynamic expression, it is
     * evaluated once into a synthetic template variable (via a compiled
     * `SetNode`), and a reference to that variable is reused instead, so
     * the expression is not re-evaluated (and is not duplicated in the
     * compiled tree, which Twig node objects do not support) at every
     * `trans` call site.
     *
     * @param TransDefaultDomainNode $node The marker node.
     * @return Node The node to keep in the compiled tree: the original
     * marker (for a literal domain, later dropped in `leaveNode()`), or a
     * `SetNode` assigning the evaluated domain to a synthetic variable.
     */
    private function replaceWithDomainAssignment(TransDefaultDomainNode $node): Node
    {
        $expr = $node->getNode('expr');

        if ($expr instanceof ConstantExpression) {
            $this->domainStack[array_key_last($this->domainStack)] = $expr;

            return $node;
        }

        $variable = '__derafu_trans_default_domain_'
            . hash('xxh128', $node->getTemplateName() . ':' . $node->getTemplateLine());

        $this->domainStack[array_key_last($this->domainStack)] =
            new ContextVariable($variable, $node->getTemplateLine());

        return new SetNode(
            false,
            new Nodes([new AssignContextVariable($variable, $node->getTemplateLine())]),
            new Nodes([$expr]),
            $node->getTemplateLine()
        );
    }

    /**
     * Injects the default domain into a `trans` filter call, unless it
     * already has one (positional or named argument).
     *
     * @param FilterExpression $node The `trans` filter call.
     * @param AbstractExpression $domain The default domain expression.
     */
    private function applyDefaultDomain(FilterExpression $node, AbstractExpression $domain): void
    {
        $arguments = $node->getNode('arguments');

        if ($arguments instanceof EmptyNode) {
            $arguments = new Nodes();
            $node->setNode('arguments', $arguments);
        }

        if ($this->hasNamedArgument($arguments)) {
            if (!$arguments->hasNode('domain') && !$arguments->hasNode('1')) {
                $arguments->setNode('domain', $domain);
            }

            return;
        }

        if ($arguments->hasNode('1')) {
            return;
        }

        if (!$arguments->hasNode('0')) {
            $arguments->setNode('0', new ArrayExpression([], $node->getTemplateLine()));
        }

        $arguments->setNode('1', $domain);
    }

    /**
     * Checks whether a `trans` filter's arguments node uses named arguments.
     *
     * @param Node $arguments The filter's arguments node.
     * @return bool Whether at least one argument is passed by name.
     */
    private function hasNamedArgument(Node $arguments): bool
    {
        foreach ($arguments as $name => $child) {
            if (!is_int($name)) {
                return true;
            }
        }

        return false;
    }
}
