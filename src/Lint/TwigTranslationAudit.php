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

use Derafu\Translation\Contract\TranslationResourceProviderInterface;
use Derafu\Translation\Lint\TranslationAudit;
use Derafu\Translation\TranslatorFactory;
use Twig\Environment;

/**
 * Checks everything that a package with templates needs to be translated, in one
 * call: what `Derafu\Translation\Lint\TranslationAudit` checks in its code, and
 * the same in its templates.
 *
 * The code is audited by `TranslationAudit`, and the templates are read with
 * `TemplateMessageScanner` (the messages they translate) and
 * `TemplateTextScanner` (the texts they write). Then both are put together: the
 * messages of the templates are checked against the same catalogues, and an entry
 * of the catalogues that only a template uses is not left over. A package without
 * templates has no use for this: it uses `TranslationAudit`.
 *
 *     $report = (new TwigTranslationAudit())->audit($src, $templates, new MyProvider(), $twig);
 *     $this->assertSame([], $report->describe($report->missingTranslations));
 *
 * It only finds facts, in a report, about what it reads: it does not say what is
 * a problem. A text that is written in a template and must not be translated (a
 * brand name) can be allowed, text by text: it is an explicit decision of whoever
 * uses this, and it is not made here.
 *
 * Part of the lint tools: it is for tools and tests, never for the code that
 * runs the package.
 */
final class TwigTranslationAudit
{
    /**
     * Audits a package.
     *
     * @param string $directory Directory with the code of the package.
     * @param string $templates Directory with the templates of the package. It
     * must be one of the paths of the loader of the environment.
     * @param TranslationResourceProviderInterface|iterable<TranslationResourceProviderInterface> $providers
     * The catalogues of the package.
     * @param Environment $twig The environment the templates are written for,
     * with its extensions and components. It must have the translation extension.
     * @param string $locale The locale of the catalogues that is checked.
     * @param list<string> $allowedThrowables Classes of exceptions that are not
     * translatable and are allowed, by their full name.
     * @param list<\Derafu\Translation\Lint\MessageMethod> $messageMethods The
     * methods of the package that receive the id of a message: their calls are
     * messages.
     * @param string|null $defaultDomain The domain of the messages of the
     * templates that do not have one: the one of the translation extension of the
     * environment, or `null` if it has none (`messages`).
     * @param list<string> $allowedTexts Texts that the templates write and are
     * allowed to not be translated, by the whole text.
     * @throws \InvalidArgumentException If a directory does not exist.
     * @throws \PhpParser\Error If a file of the code can not be parsed.
     * @throws \Twig\Error\Error If a template can not be loaded or parsed.
     */
    public function audit(
        string $directory,
        string $templates,
        TranslationResourceProviderInterface|iterable $providers,
        Environment $twig,
        string $locale = 'es',
        array $allowedThrowables = [],
        array $messageMethods = [],
        ?string $defaultDomain = null,
        array $allowedTexts = []
    ): TwigTranslationAuditReport {
        // The catalogues are read twice, by this and by the audit of the code.
        $providers = $providers instanceof TranslationResourceProviderInterface
            ? [$providers]
            : iterator_to_array($providers, false);

        $code = (new TranslationAudit())->audit($directory, $providers, $locale, $allowedThrowables, $messageMethods);

        $references = (new TemplateMessageScanner($twig, $defaultDomain))->scanDirectory($templates);
        $texts = array_values(array_filter(
            (new TemplateTextScanner($twig))->scanDirectory($templates),
            fn (TemplateText $text) => !in_array($text->text, $allowedTexts, true)
        ));

        $catalogue = TranslatorFactory::create($locale, [], $providers)->getCatalogue($locale);

        $dynamic = $code->dynamicMessages;
        $missing = $code->missingTranslations;
        $used = [];
        foreach ($references as $reference) {
            if ($reference->isDynamic()) {
                $dynamic[] = $reference;

                continue;
            }

            $used[$this->domain((string) $reference->domain)][] = (string) $reference->id;
            if (!$catalogue->has((string) $reference->id, (string) $reference->domain)) {
                $missing[] = $reference;
            }
        }

        $notUsed = array_values(array_filter(
            $code->notUsedBySources,
            fn (array $entry) => !in_array($entry['id'], $used[$this->domain($entry['domain'])] ?? [], true)
        ));

        return new TwigTranslationAuditReport(
            $directory,
            $templates,
            $dynamic,
            $missing,
            $notUsed,
            $code->notTranslatable,
            $texts,
            $code->nothingFound && $references === []
        );
    }

    /**
     * The domain without the suffix of the ICU format: `content+intl-icu` is the
     * domain `content`.
     */
    private function domain(string $domain): string
    {
        return preg_replace('/\+intl-icu$/', '', $domain) ?? $domain;
    }
}
