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

use Derafu\Translation\TranslatableMessage;
use Derafu\Twig\NodeVisitor\TranslationDefaultDomainNodeVisitor;
use Derafu\Twig\TokenParser\TransDefaultDomainTokenParser;
use Derafu\Twig\TokenParser\TransTokenParser;
use Symfony\Contracts\Translation\TranslatorInterface;
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
 * The `{% trans %}` tag CANNOT be used for an ICU message containing a
 * plural/select construct (e.g. `{count, plural, one {# item} other {#
 * items}}`): Twig's own lexer treats a literal `{#` anywhere in template
 * text as the start of a `{# comment #}`, before this extension's code ever
 * runs, breaking the whole template (or worse, silently swallowing content
 * up to the next `#}`, if the message happens to contain one). This is not
 * a limitation of this extension: it applies to any raw template text, and
 * is unrelated to translation. It does not affect the `trans`/`t()` message
 * argument, since a Twig string literal is lexed literally and is not
 * scanned for `{{`/`{%`/`{#`. Any ICU message with a plural/select
 * construct must go through `trans`/`t()`, never through `{% trans %}`.
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
     * @param string $message The message to translate.
     * @param array<string, mixed> $parameters Parameters for translation
     * placeholders.
     * @param string|null $domain The translation domain, or `null` to use
     * this extension's default domain.
     * @param string|null $locale The locale to translate to, or `null` to
     * use this extension's default locale.
     * @return string The translated message.
     */
    public function trans(
        string $message,
        array $parameters = [],
        ?string $domain = null,
        ?string $locale = null
    ): string {
        $translatable = $this->createTranslatable(
            $message,
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
