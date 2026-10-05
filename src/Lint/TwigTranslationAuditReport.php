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

use Derafu\Translation\Lint\MessageReference;
use Derafu\Translation\Lint\ThrowReference;
use Derafu\Translation\Lint\TranslationAuditReport;

/**
 * What an audit of a package with templates found.
 *
 * It is the report of `Derafu\Translation\Lint\TranslationAuditReport`, with
 * what the templates add to it, and it only has facts too: it does not say
 * whether any of them is a problem, that is for the test of each package to say,
 * by asserting that the lists it cares about are empty.
 *
 *     $this->assertSame([], $report->describe($report->untranslatedTexts));
 *
 * Part of the lint tools: it is for tools and tests, never for the code that
 * runs the package.
 */
final readonly class TwigTranslationAuditReport
{
    /**
     * @param string $directory The directory with the code that was audited.
     * @param string $templates The directory with the templates that were
     * audited.
     * @param list<MessageReference> $dynamicMessages Messages, of the code or of
     * the templates, that are not a literal, so they can not be checked by
     * reading them.
     * @param list<MessageReference> $missingTranslations Messages, of the code or
     * of the templates, that have no entry in the catalogues, in their domain.
     * @param list<array{domain: string, id: string}> $notUsedBySources Entries of
     * the catalogues, in any domain, that no message of the code or of the
     * templates that were read uses.
     * @param list<ThrowReference> $notTranslatable Exceptions that are not
     * translatable and were not allowed.
     * @param list<TemplateText> $untranslatedTexts Texts that the templates
     * write, that a person can read, and that do not go through the translation.
     * @param bool $nothingFound Whether no message was found at all, in the code
     * or in the templates.
     */
    public function __construct(
        public string $directory,
        public string $templates,
        public array $dynamicMessages,
        public array $missingTranslations,
        public array $notUsedBySources,
        public array $notTranslatable,
        public array $untranslatedTexts,
        public bool $nothingFound
    ) {
    }

    /**
     * Turns findings into lines, one each, for the message of a failed test.
     *
     * It says where each one is (relative to the audited directory) and what it
     * is, like `TranslationAuditReport::describe()` does, and a text of a
     * template by the text and where it is (`text`, or the attribute). It does
     * not say whether they are a problem.
     *
     * @param list<MessageReference|ThrowReference|TemplateText|array{domain: string, id: string}> $findings
     * @return list<string>
     */
    public function describe(array $findings): array
    {
        $code = new TranslationAuditReport($this->directory, [], [], [], [], false);

        $lines = [];
        foreach ($findings as $finding) {
            $lines[] = match (true) {
                $finding instanceof TemplateText => sprintf(
                    '%s "%s" [%s]',
                    $this->where($finding->file, $finding->line),
                    $finding->text,
                    $finding->kind
                ),
                $finding instanceof MessageReference && $this->isOfATemplate($finding->file) => $this->where($finding->file, $finding->line) . ' ' . (
                    $finding->isDynamic()
                        ? $finding->identity()
                        : sprintf('"%s" [%s]', $finding->id, $finding->domain)
                ),
                default => $code->describe([$finding])[0],
            };
        }

        return $lines;
    }

    private function isOfATemplate(string $file): bool
    {
        return str_starts_with($file, $this->templatesPrefix());
    }

    private function where(string $file, int $line): string
    {
        $prefix = $this->templatesPrefix();

        return sprintf('%s:%d', str_starts_with($file, $prefix) ? substr($file, strlen($prefix)) : $file, $line);
    }

    private function templatesPrefix(): string
    {
        return rtrim(realpath($this->templates) ?: $this->templates, '/') . '/';
    }
}
