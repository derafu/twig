<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Extension;

use Derafu\Translation\TranslatableMessage;
use Derafu\Twig\Extension\TranslationExtension;
use Derafu\Twig\Node\TransDefaultDomainNode;
use Derafu\Twig\Node\TransNode;
use Derafu\Twig\NodeVisitor\TranslationDefaultDomainNodeVisitor;
use Derafu\Twig\TokenParser\TransDefaultDomainTokenParser;
use Derafu\Twig\TokenParser\TransTokenParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;
use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader as TwigArrayLoader;

#[CoversClass(TranslationExtension::class)]
#[CoversClass(TransDefaultDomainNode::class)]
#[CoversClass(TransDefaultDomainTokenParser::class)]
#[CoversClass(TranslationDefaultDomainNodeVisitor::class)]
#[CoversClass(TransNode::class)]
#[CoversClass(TransTokenParser::class)]
class TranslationExtensionTest extends TestCase
{
    private function createTranslator(): Translator
    {
        $translator = new Translator('es');
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource(
            'array',
            ['Hello {name}!' => 'Hola {name}!'],
            'es',
            'messages+intl-icu'
        );
        $translator->addResource(
            'array',
            ['Bye {name}!' => 'Chau {name}!'],
            'es',
            'other+intl-icu'
        );
        $translator->addResource(
            'array',
            ['Save' => 'Guardar'],
            'es',
            'messages+intl-icu'
        );
        $translator->addResource(
            'array',
            ['Hello {name}!' => 'Bonjour {name}!'],
            'fr',
            'messages+intl-icu'
        );

        return $translator;
    }

    private function render(string $template, array $context, TranslationExtension $extension): string
    {
        $twig = new Environment(new TwigArrayLoader(['t.html.twig' => $template]));
        $twig->addExtension($extension);

        return $twig->render('t.html.twig', $context);
    }

    public function testTransFilterWithTranslator(): void
    {
        $extension = new TranslationExtension($this->createTranslator());

        $html = $this->render(
            "{{ 'Hello {name}!'|trans({'name': who}, 'messages+intl-icu') }}",
            ['who' => 'Juan'],
            $extension
        );

        $this->assertSame('Hola Juan!', $html);
    }

    public function testTransFilterFallsBackToIcuWithoutTranslator(): void
    {
        $extension = new TranslationExtension();

        $html = $this->render(
            "{{ 'Hello {name}!'|trans({'name': who}) }}",
            ['who' => 'Juan'],
            $extension
        );

        $this->assertSame('Hello Juan!', $html);
    }

    public function testTFunctionCreatesTranslatableMessage(): void
    {
        $extension = new TranslationExtension(null, null, 'en');

        $translatable = $extension->createTranslatable('Hello {name}!', ['name' => 'Juan']);

        $this->assertInstanceOf(TranslatableMessage::class, $translatable);
        $this->assertSame('Hello Juan!', (string) $translatable);
    }

    public function testDefaultDomainTagAppliesToFollowingTransCalls(): void
    {
        $extension = new TranslationExtension($this->createTranslator());

        $html = $this->render(
            "{% trans_default_domain 'messages+intl-icu' %}"
            . "{{ 'Hello {name}!'|trans({'name': who}) }}",
            ['who' => 'Juan'],
            $extension
        );

        $this->assertSame('Hola Juan!', $html);
    }

    public function testExplicitDomainOverridesDefaultDomainTag(): void
    {
        $extension = new TranslationExtension($this->createTranslator());

        $html = $this->render(
            "{% trans_default_domain 'messages+intl-icu' %}"
            . "{{ 'Bye {name}!'|trans({'name': who}, 'other+intl-icu') }}",
            ['who' => 'Juan'],
            $extension
        );

        $this->assertSame('Chau Juan!', $html);
    }

    public function testDefaultDomainTagIsScopedToItsBlock(): void
    {
        $extension = new TranslationExtension($this->createTranslator());

        $template = "{% trans_default_domain 'messages+intl-icu' %}"
            . '{% block inner %}'
            . "{% trans_default_domain 'other+intl-icu' %}"
            . "{{ 'Bye {name}!'|trans({'name': who}) }}"
            . '{% endblock %}'
            . "|{{ 'Hello {name}!'|trans({'name': who}) }}";

        $html = $this->render($template, ['who' => 'Juan'], $extension);

        $this->assertSame('Chau Juan!|Hola Juan!', $html);
    }

    public function testDefaultDomainTagAcceptsADynamicExpression(): void
    {
        $extension = new TranslationExtension($this->createTranslator());

        $html = $this->render(
            '{% trans_default_domain domainVariable %}'
            . "{{ 'Hello {name}!'|trans({'name': who}) }}",
            ['who' => 'Juan', 'domainVariable' => 'messages+intl-icu'],
            $extension
        );

        $this->assertSame('Hola Juan!', $html);
    }

    public function testDefaultDomainTagEvaluatesADynamicExpressionOnlyOnce(): void
    {
        $extension = new TranslationExtension($this->createTranslator());
        $counter = new class () {
            public int $count = 0;

            public function domain(): string
            {
                $this->count++;

                return 'messages+intl-icu';
            }
        };

        $html = $this->render(
            '{% trans_default_domain counter.domain() %}'
            . "{{ 'Hello {name}!'|trans({'name': who}) }}"
            . "{{ 'Hello {name}!'|trans({'name': who}) }}",
            ['who' => 'Juan', 'counter' => $counter],
            $extension
        );

        $this->assertSame('Hola Juan!Hola Juan!', $html);
        $this->assertSame(1, $counter->count);
    }

    public function testTransTagWithExplicitWithAndDomain(): void
    {
        $extension = new TranslationExtension($this->createTranslator());

        $html = $this->render(
            "{% trans with {'name': who} from 'messages+intl-icu' %}Hello {name}!{% endtrans %}",
            ['who' => 'Juan'],
            $extension
        );

        $this->assertSame('Hola Juan!', $html);
    }

    public function testTransTagWithoutWithOrDomain(): void
    {
        $extension = new TranslationExtension($this->createTranslator(), 'messages+intl-icu');

        $html = $this->render('{% trans %}Save{% endtrans %}', [], $extension);

        $this->assertSame('Guardar', $html);
    }

    public function testTransTagFallsBackToIcuWithoutTranslator(): void
    {
        $extension = new TranslationExtension();

        $html = $this->render(
            "{% trans with {'name': who} %}Hello {name}!{% endtrans %}",
            ['who' => 'Juan'],
            $extension
        );

        $this->assertSame('Hello Juan!', $html);
    }

    public function testTransTagInheritsDefaultDomainTag(): void
    {
        $extension = new TranslationExtension($this->createTranslator());

        $html = $this->render(
            "{% trans_default_domain 'messages+intl-icu' %}"
            . "{% trans with {'name': who} %}Hello {name}!{% endtrans %}",
            ['who' => 'Juan'],
            $extension
        );

        $this->assertSame('Hola Juan!', $html);
    }

    public function testTransTagWithLocale(): void
    {
        $extension = new TranslationExtension($this->createTranslator());

        $html = $this->render(
            "{% trans with {'name': who} from 'messages+intl-icu' into 'fr' %}Hello {name}!{% endtrans %}",
            ['who' => 'Juan'],
            $extension
        );

        $this->assertSame('Bonjour Juan!', $html);
    }

    public function testTransTagRejectsInterpolatedBody(): void
    {
        $extension = new TranslationExtension();
        $twig = new Environment(new TwigArrayLoader([
            'bad.html.twig' => '{% trans %}Hello {{ who }}!{% endtrans %}',
        ]));
        $twig->addExtension($extension);

        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage('must be plain text');

        $twig->render('bad.html.twig', ['who' => 'Juan']);
    }
}
