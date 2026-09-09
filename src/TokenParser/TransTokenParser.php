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

use Derafu\Twig\Node\TransNode;
use Twig\Error\SyntaxError;
use Twig\Node\Node;
use Twig\Node\TextNode;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Token parser for the `{% trans %}...{% endtrans %}` block tag:
 *
 *     {% trans %}Hello {name}!{% endtrans %}
 *     {% trans with {'name': user.name} %}Hello {name}!{% endtrans %}
 *     {% trans with {'name': user.name} from 'content+intl-icu' %}Hello {name}!{% endtrans %}
 *     {% trans with {'name': user.name} from 'content+intl-icu' into 'fr' %}Hello {name}!{% endtrans %}
 *
 * Unlike Symfony's own `trans` tag, parameters are never auto-detected by
 * scanning the message text: they must always be passed explicitly through
 * `with`. This is deliberate, not a missing feature — see `TransNode`.
 *
 * Do not use this tag for an ICU message with a plural/select construct
 * (e.g. `{count, plural, ...}`): a literal `{#` in the message text is
 * lexed by Twig itself as a comment start, before this parser ever runs.
 * Use the `trans`/`t()` filter/function instead, whose message is a Twig
 * string literal and is never affected by this. See
 * `Derafu\Twig\Extension\TranslationExtension`.
 */
final class TransTokenParser extends AbstractTokenParser
{
    /**
     * {@inheritDoc}
     */
    public function parse(Token $token): Node
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $with = null;
        $domain = null;
        $locale = null;

        if (!$stream->test(Token::BLOCK_END_TYPE)) {
            if ($stream->test('with')) {
                // {% trans with {'name': user.name} %}
                $stream->next();
                $with = $this->parser->getExpressionParser()->parseExpression();
            }

            if ($stream->test('from')) {
                // {% trans from 'content+intl-icu' %}
                $stream->next();
                $domain = $this->parser->getExpressionParser()->parseExpression();
            }

            if ($stream->test('into')) {
                // {% trans into 'fr' %}
                $stream->next();
                $locale = $this->parser->getExpressionParser()->parseExpression();
            } elseif (!$stream->test(Token::BLOCK_END_TYPE)) {
                throw new SyntaxError(
                    'Unexpected token. Twig was looking for the "with", "from", or "into" keyword.',
                    $stream->getCurrent()->getLine(),
                    $stream->getSourceContext()
                );
            }
        }

        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse($this->decideTransFork(...), true);

        if (!$body instanceof TextNode) {
            throw new SyntaxError(
                'The message inside a "trans" tag must be plain text: '
                . 'interpolating a Twig expression would make the '
                . 'translation id vary at runtime and never match an entry '
                . 'in the translation catalogue. Pass values through "with" '
                . 'instead, e.g. {% trans with {\'name\': user.name} %}Hello '
                . '{name}!{% endtrans %}.',
                $body->getTemplateLine(),
                $stream->getSourceContext()
            );
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        return new TransNode($body, $with, $domain, $locale, $lineno);
    }

    /**
     * Decides whether the current token ends the `trans` block.
     *
     * @param Token $token The current token.
     * @return bool Whether the token is `endtrans`.
     */
    public function decideTransFork(Token $token): bool
    {
        return $token->test(['endtrans']);
    }

    /**
     * {@inheritDoc}
     */
    public function getTag(): string
    {
        return 'trans';
    }
}
