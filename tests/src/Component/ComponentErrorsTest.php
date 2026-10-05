<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Component;

use Derafu\Translation\TranslatorFactory;
use Derafu\Twig\Abstract\AbstractComponent;
use Derafu\Twig\Component\Block\AccordionComponent;
use Derafu\Twig\Component\Block\TimelineComponent;
use Derafu\Twig\Exception\TwigComponentException;
use Derafu\Twig\Exception\TwigException;
use Derafu\Twig\Translation\TwigTranslationResourceProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

/**
 * The error of a component names the component and says what is wrong, and both
 * are translated.
 */
#[CoversClass(AbstractComponent::class)]
#[CoversClass(AccordionComponent::class)]
#[CoversClass(TimelineComponent::class)]
#[UsesClass(TwigComponentException::class)]
#[UsesClass(TwigException::class)]
#[UsesClass(TwigTranslationResourceProvider::class)]
final class ComponentErrorsTest extends TestCase
{
    /**
     * @return array<string, array{callable(): mixed, string, string}>
     */
    public static function provideErrors(): array
    {
        return [
            'a message without parameters' => [
                fn () => (new AccordionComponent())->setItems([['content' => 'Content']]),
                'Component block-accordion: Item title is required.',
                'Componente block-accordion: El título del ítem es obligatorio.',
            ],
            'a message with a parameter' => [
                fn () => (new TimelineComponent())->setEvents([['date' => 20240101]]),
                'Component block-timeline: Invalid date format for event: 20240101',
                'Componente block-timeline: Formato de fecha inválido para el evento: 20240101',
            ],
            'a message with a parameter that has no value' => [
                fn () => (new TimelineComponent())->setEvents([['date' => null]]),
                'Component block-timeline: Invalid date format for event: N/A',
                'Componente block-timeline: Formato de fecha inválido para el evento: N/A',
            ],
        ];
    }

    /**
     * @param callable(): mixed $call
     */
    #[DataProvider('provideErrors')]
    public function testTheErrorIsThrownAndTranslated(callable $call, string $english, string $spanish): void
    {
        try {
            $call();
            $this->fail('The component did not throw its error.');
        } catch (TwigComponentException $exception) {
            $translator = TranslatorFactory::create('es', [], [new TwigTranslationResourceProvider()]);

            $this->assertSame($english, $exception->getMessage());
            $this->assertSame($spanish, $exception->trans($translator));
        }
    }
}
