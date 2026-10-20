<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Translation;

use Derafu\Translation\Lint\MessageReference;
use Derafu\Translation\Lint\TranslationAudit;
use Derafu\Twig\Translation\TwigTranslationResourceProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Twig\Error\SyntaxError;

/**
 * The package is translated: every message has its Spanish translation, the
 * catalogue has nothing the code does not use, and every exception that the
 * package throws is translatable, except the one that Twig requires.
 *
 * The package has no templates (the components are in `derafu/twig-components`),
 * so only its code is read.
 *
 * The only message that can not be a literal is the one that the `trans` filter
 * and the `t()` function of the templates give to a `TranslatableMessage`: it is
 * the message of the template that calls them. It is fixed here by its function
 * and its whole call, so any other message that is not a literal makes this test
 * fail.
 */
#[CoversClass(TwigTranslationResourceProvider::class)]
final class TwigMessagesTest extends TestCase
{
    public function testThePackageIsTranslated(): void
    {
        $report = (new TranslationAudit())->audit(
            dirname(__DIR__, 3) . '/src',
            new TwigTranslationResourceProvider(),
            allowedThrowables: [
                // Twig requires its own exception when the syntax of a tag is wrong.
                SyntaxError::class,
            ]
        );

        // Finding nothing would look like a clean result.
        $this->assertFalse($report->nothingFound);
        $this->assertSame([], $report->describe($report->missingTranslations));
        $this->assertSame([], $report->describe($report->notUsedBySources));
        $this->assertSame([], $report->describe($report->notTranslatable));

        $this->assertSame(
            [
                'Derafu\\Twig\\Extension\\TranslationExtension::createTranslatable: '
                    . 'new \\Derafu\\Translation\\TranslatableMessage($message, $parameters, $domain ?? $this->domain, $locale ?? $this->locale)',
            ],
            array_map(fn (MessageReference $reference) => $reference->identity(), $report->dynamicMessages)
        );
    }
}
