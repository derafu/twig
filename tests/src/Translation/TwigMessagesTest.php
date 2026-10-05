<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Translation;

use Derafu\Translation\Lint\MessageMethod;
use Derafu\Translation\Lint\MessageReference;
use Derafu\Twig\Abstract\AbstractComponent;
use Derafu\Twig\Cache\CacheItemPool;
use Derafu\Twig\Extension\TranslationExtension;
use Derafu\Twig\Lint\TemplateFinder;
use Derafu\Twig\Lint\TemplateMessageScanner;
use Derafu\Twig\Lint\TemplateSource;
use Derafu\Twig\Lint\TemplateText;
use Derafu\Twig\Lint\TemplateTextScanner;
use Derafu\Twig\Lint\TwigTranslationAudit;
use Derafu\Twig\Lint\TwigTranslationAuditReport;
use Derafu\Twig\Node\TransDefaultDomainNode;
use Derafu\Twig\Node\TransNode;
use Derafu\Twig\NodeVisitor\TranslationDefaultDomainNodeVisitor;
use Derafu\Twig\Service\ComponentRegistrar;
use Derafu\Twig\Service\TwigCreator;
use Derafu\Twig\TokenParser\TransDefaultDomainTokenParser;
use Derafu\Twig\TokenParser\TransTokenParser;
use Derafu\Twig\Translation\TwigTranslationResourceProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Twig\Error\SyntaxError;

/**
 * The package is translated: every message has its Spanish translation, the
 * catalogue has nothing the code or the templates do not use, every text that the
 * templates write goes through the translation, and every exception that the
 * package throws is translatable, except the one that Twig requires.
 *
 * The only message that can not be a literal is the one that the `trans` filter
 * and the `t()` function of the templates give to a `TranslatableMessage`: it is
 * the message of the template that calls them. It is fixed here by its function
 * and its whole call, so any other message that is not a literal makes this test
 * fail.
 */
#[CoversClass(TwigTranslationResourceProvider::class)]
#[UsesClass(TwigTranslationAudit::class)]
#[UsesClass(TwigTranslationAuditReport::class)]
#[UsesClass(TemplateMessageScanner::class)]
#[UsesClass(TemplateTextScanner::class)]
#[UsesClass(TemplateText::class)]
#[UsesClass(TemplateFinder::class)]
#[UsesClass(TemplateSource::class)]
#[UsesClass(TranslationExtension::class)]
#[UsesClass(TransNode::class)]
#[UsesClass(TransDefaultDomainNode::class)]
#[UsesClass(TranslationDefaultDomainNodeVisitor::class)]
#[UsesClass(TransTokenParser::class)]
#[UsesClass(TransDefaultDomainTokenParser::class)]
#[UsesClass(TwigCreator::class)]
#[UsesClass(ComponentRegistrar::class)]
#[UsesClass(CacheItemPool::class)]
final class TwigMessagesTest extends TestCase
{
    public function testThePackageIsTranslated(): void
    {
        $root = dirname(__DIR__, 3);

        $report = (new TwigTranslationAudit())->audit(
            $root . '/src',
            $root . '/resources/templates',
            new TwigTranslationResourceProvider(),
            (new TwigCreator())->create(['paths' => [$root . '/resources/templates'], 'extra' => false]),
            allowedThrowables: [
                // Twig requires its own exception when the syntax of a tag is wrong.
                SyntaxError::class,
            ],
            messageMethods: [
                new MessageMethod(AbstractComponent::class, 'error', domain: 'errors'),
            ]
        );

        // Finding nothing would look like a clean result.
        $this->assertFalse($report->nothingFound);
        $this->assertSame([], $report->describe($report->missingTranslations));
        $this->assertSame([], $report->describe($report->notUsedBySources));
        $this->assertSame([], $report->describe($report->notTranslatable));
        $this->assertSame([], $report->describe($report->untranslatedTexts));

        $this->assertSame(
            [
                'Derafu\\Twig\\Extension\\TranslationExtension::createTranslatable: '
                    . 'new \\Derafu\\Translation\\TranslatableMessage($message, $parameters, $domain ?? $this->domain, $locale ?? $this->locale)',
            ],
            array_map(fn (MessageReference $reference) => $reference->identity(), $report->dynamicMessages)
        );
    }
}
