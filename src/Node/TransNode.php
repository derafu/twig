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

use Derafu\Twig\Extension\TranslationExtension;
use Twig\Attribute\YieldReady;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;
use Twig\Node\TextNode;

/**
 * Node for the `{% trans %}...{% endtrans %}` block tag.
 *
 * Unlike Symfony's own `TransNode`, the message is never scanned for
 * placeholders: the body must be plain text (enforced by
 * `TransTokenParser`), used verbatim as the translation id, and parameters
 * are always passed explicitly through `with`. This keeps the message
 * compatible with ICU syntax (`{name}`, and nested `{count, plural, ...}`),
 * which a text scan looking for placeholders would otherwise misinterpret.
 */
#[YieldReady]
final class TransNode extends Node
{
    public function __construct(
        TextNode $body,
        ?AbstractExpression $with = null,
        ?AbstractExpression $domain = null,
        ?AbstractExpression $locale = null,
        int $lineno = 0
    ) {
        $nodes = [];
        if ($with !== null) {
            $nodes['with'] = $with;
        }
        if ($domain !== null) {
            $nodes['domain'] = $domain;
        }
        if ($locale !== null) {
            $nodes['locale'] = $locale;
        }

        parent::__construct($nodes, ['message' => $body->getAttribute('data')], $lineno);
    }

    /**
     * {@inheritDoc}
     */
    public function compile(Compiler $compiler): void
    {
        $compiler->addDebugInfo($this);

        $compiler
            ->write('yield $this->env->getExtension(')
            ->repr(TranslationExtension::class)
            ->raw(')->trans(')
            ->repr($this->getAttribute('message'))
            ->raw(', ')
        ;

        if ($this->hasNode('with')) {
            $compiler->subcompile($this->getNode('with'));
        } else {
            $compiler->raw('[]');
        }

        $compiler->raw(', ');

        if ($this->hasNode('domain')) {
            $compiler->subcompile($this->getNode('domain'));
        } else {
            $compiler->raw('null');
        }

        if ($this->hasNode('locale')) {
            $compiler->raw(', ')->subcompile($this->getNode('locale'));
        }

        $compiler->raw(");\n");
    }
}
