<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\TokenParser;

use Derafu\Twig\Node\TransDefaultDomainNode;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Token parser for the `{% trans_default_domain 'domain' %}` tag.
 */
final class TransDefaultDomainTokenParser extends AbstractTokenParser
{
    /**
     * {@inheritDoc}
     */
    public function parse(Token $token): Node
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();

        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        return new TransDefaultDomainNode($expr, $token->getLine());
    }

    /**
     * {@inheritDoc}
     */
    public function getTag(): string
    {
        return 'trans_default_domain';
    }
}
