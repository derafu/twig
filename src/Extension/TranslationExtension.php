<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Extension;

use Derafu\Translation\Contract\TranslatableInterface;
use Derafu\Translation\TranslatableMessage;
use Derafu\Twig\Exception\TwigException;
use Derafu\Twig\NodeVisitor\TranslationDefaultDomainNodeVisitor;
use Derafu\Twig\TokenParser\TransDefaultDomainTokenParser;
use Derafu\Twig\TokenParser\TransTokenParser;
use Stringable;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Twig extension to enable message translation within templates.
 *
 * This extension provides a `trans` filter, a `t()` function, a
 * `{% trans %}...{% endtrans %}` block tag, and a `{% trans_default_domain %}`
 * tag, matching the structure of Symfony's own Twig translation integration.
 * Unlike `symfony/twig-bridge`, it depends only on this ecosystem's own
 * translation component instead of pulling in the full twig-bridge package.
 *
 * Unlike Symfony's `{% trans %}` tag, the block tag here never scans the
 * message text for placeholders (Symfony's own tag looks for `%name%`
 * occurrences): parameters are always passed explicitly through `with`. This
 * keeps it compatible with simple ICU messages (`{name}`), unlike Symfony's
 * own text-scanning approach, which was never designed for ICU at all.
 *
 * A literal `{#` anywhere in raw template text (outside a string literal)
 * is always lexed by Twig itself as the start of a `{# comment #}`, before
 * this extension's code ever runs — so the `{% trans %}` tag CANNOT be used
 * for an ICU message with a plural/select construct that uses the bare `#`
 * shorthand (e.g. `{count, plural, one {# item} other {# items}}`): it
 * breaks the whole template (or worse, silently swallows content up to the
 * next `#}`, if the message happens to contain one). This is not a
 * limitation of this extension; it is unrelated to translation.
 *
 * In an environment with only Twig's own extensions, a `{#` inside a
 * *string literal* argument to `trans`/`t()` is safe (the string is lexed
 * literally, not scanned for `{{`/`{%`/`{#`). **This safety does not
 * necessarily hold once `symfony/ux-twig-component` is registered** (the
 * runtime behind `<twig:...>` component tags): a real case was found where
 * the exact same `{#` inside a `trans` filter's string argument, in a
 * template that also uses a `<twig:...>` component and `{% extends %}`,
 * produced `Twig\Error\SyntaxError: A template that extends another one
 * cannot include content outside Twig blocks` — an unrelated-sounding
 * error, with no `ux-twig-component`-specific message to point at the real
 * cause. The exact mechanism inside `ux-twig-component` was not identified
 * (it is a third-party dependency, not code in this package), but the
 * trigger was confirmed by bisection to be exactly the `{#` substring.
 *
 * The fix is NOT to replace `#` with a literal `{count}` reference to the
 * same plural argument: real ICU (via PHP's `MessageFormatter`, used by
 * both the fallback and, through Symfony, by a real translator) throws
 * `U_ARGUMENT_TYPE_MISMATCH` when the same argument name is used both as
 * the plural selector and as a literal `{name}` reference inside a branch
 * — `#` exists specifically to avoid that conflict, it is not
 * interchangeable with a named reference. **The safe fix is to pass the
 * same value under two different argument names**: one used only for
 * plural selection, one only for display, e.g.
 * `{count, plural, one {{n} item} other {{n} items}}` called as
 * `trans({'count': value, 'n': value})`.
 *
 * When no translator is configured, `trans` falls back to
 * `TranslatableMessage::__toString()`, which still applies ICU formatting.
 * This differs from `symfony/twig-bridge`, whose no-translator fallback uses
 * a plain `strtr()`-based identity translator that does not understand ICU.
 *
 * This extension never assumes a domain on its own: a message translated
 * through `trans`/`t()` without an explicit domain, and outside the scope of
 * a `{% trans_default_domain %}` tag, uses whatever "no domain" means for the
 * underlying translator (its own configured default domain, if any). The
 * `$domain` constructor parameter is an opt-in fallback for callers that
 * register this extension without using the tag; it is never required.
 */
final class TranslationExtension extends AbstractExtension
{
    /**
     * Constructor.
     *
     * @param TranslatorInterface|null $translator The translator to use, or
     * `null` to fall back to ICU formatting without translation.
     * @param string|null $domain The default translation domain to use when
     * none is passed to `trans`/`t()`.
     * @param string|null $locale The default locale to use when none is
     * passed to `trans`/`t()`.
     */
    public function __construct(
        private readonly ?TranslatorInterface $translator = null,
        private readonly ?string $domain = null,
        private readonly ?string $locale = null,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('trans', [$this, 'trans']),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('t', [$this, 'createTranslatable']),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getTokenParsers(): array
    {
        return [
            // {% trans %}Hello {name}!{% endtrans %}
            new TransTokenParser(),

            // {% trans_default_domain 'content+intl-icu' %}
            new TransDefaultDomainTokenParser(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getNodeVisitors(): array
    {
        return [
            new TranslationDefaultDomainNodeVisitor(),
        ];
    }

    /**
     * Translates a message.
     *
     * The message is a text, which is its own translation id, or a translatable
     * value (a `TranslatableMessage`, the result of `t()`, a translatable
     * exception), which brings its own id, parameters and domain: it is
     * translated as it is. Without a translator a translatable value is
     * formatted as its text (the message of an exception, never the dump that PHP
     * makes of it). Like in Symfony, parameters can not be given for a message
     * that is a translatable value, because they would not be used. The domain is
     * not checked: the `trans_default_domain` tag puts one in every call.
     *
     * @param string|Stringable|TranslatableInterface|null $message The message
     * to translate. `null` and an empty text are an empty text.
     * @param array<string, mixed> $parameters Parameters for translation
     * placeholders.
     * @param string|null $domain The translation domain, or `null` to use
     * this extension's default domain.
     * @param string|null $locale The locale to translate to, or `null` to
     * use this extension's default locale.
     * @return string The translated message.
     * @throws TwigException If parameters are given for a translatable value.
     */
    public function trans(
        string|Stringable|TranslatableInterface|null $message,
        array $parameters = [],
        ?string $domain = null,
        ?string $locale = null
    ): string {
        if ($message instanceof TranslatableInterface) {
            if ($parameters !== []) {
                throw new TwigException(
                    'The "trans" filter does not take parameters for a message that is already translatable: they are the ones of the message.'
                );
            }

            if ($this->translator === null) {
                return $message instanceof Throwable
                    ? $message->getMessage()
                    : (string) $message;
            }

            return $message->trans($this->translator, $locale ?? $this->locale);
        }

        $translatable = $this->createTranslatable(
            (string) $message,
            $parameters,
            $domain,
            $locale
        );

        if ($this->translator === null) {
            return (string) $translatable;
        }

        return $translatable->trans($this->translator, $locale ?? $this->locale);
    }

    /**
     * Creates a translatable message without translating it yet.
     *
     * Useful to pass a message to something that will translate it later,
     * possibly with a different locale.
     *
     * @param string $message The message to translate.
     * @param array<string, mixed> $parameters Parameters for translation
     * placeholders.
     * @param string|null $domain The translation domain, or `null` to use
     * this extension's default domain.
     * @param string|null $locale The locale to translate to, or `null` to
     * use this extension's default locale.
     * @return TranslatableMessage The translatable message.
     */
    public function createTranslatable(
        string $message,
        array $parameters = [],
        ?string $domain = null,
        ?string $locale = null
    ): TranslatableMessage {
        return new TranslatableMessage(
            $message,
            $parameters,
            $domain ?? $this->domain,
            $locale ?? $this->locale
        );
    }
}
