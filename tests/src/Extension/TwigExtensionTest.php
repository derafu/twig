<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Extension;

use Derafu\Twig\Extension\TwigExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * The `twig` filter renders a string as a template, with the context of the one
 * that calls it.
 */
#[CoversClass(TwigExtension::class)]
final class TwigExtensionTest extends TestCase
{
    public function testAStringIsRenderedWithTheContextOfTheTemplate(): void
    {
        $twig = new Environment(new ArrayLoader(['t' => "{{ 'Hello {{ name }}'|twig }}"]));
        $twig->addExtension(new TwigExtension());

        $this->assertSame('Hello World', $twig->render('t', ['name' => 'World']));
    }

    public function testTheResultIsNotEscaped(): void
    {
        $twig = new Environment(new ArrayLoader(['t' => "{{ '<b>{{ name }}</b>'|twig }}"]), ['autoescape' => 'html']);
        $twig->addExtension(new TwigExtension());

        $this->assertSame('<b>World</b>', $twig->render('t', ['name' => 'World']));
    }
}
