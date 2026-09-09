<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Node;

use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;

/**
 * Marker node for the `{% trans_default_domain %}` tag.
 *
 * It compiles to nothing on its own: `TranslationDefaultDomainNodeVisitor`
 * removes it from the tree after using it to inject its domain expression
 * into the `trans` filter calls that follow it in the same scope.
 */
final class TransDefaultDomainNode extends Node
{
    public function __construct(AbstractExpression $expr, int $lineno = 0)
    {
        parent::__construct(['expr' => $expr], [], $lineno);
    }

    /**
     * {@inheritDoc}
     */
    public function compile(Compiler $compiler): void
    {
        // No-op: this node is only a marker used by
        // TranslationDefaultDomainNodeVisitor during AST traversal.
    }
}
