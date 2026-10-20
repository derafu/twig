<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Extension;

use Derafu\Routing\Contract\RouterInterface;
use Derafu\Routing\Router;
use Derafu\Routing\ValueObject\RequestContext;
use Derafu\Translation\Contract\TranslatableInterface;
use Derafu\Twig\Extension\RoutingExtension;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Throwable;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\TwigFunction;

/**
 * The route functions of the templates, over a router.
 */
#[CoversClass(RoutingExtension::class)]
final class RoutingExtensionTest extends TestCase
{
    public function testAnActivePathWithoutContextIsATranslatableError(): void
    {
        $router = $this->createStub(RouterInterface::class);
        $router->method('getContext')->willReturn(null);

        $exception = null;
        try {
            (new RoutingExtension($router))->isActivePath('home');
        } catch (Throwable $e) {
            $exception = $e;
        }

        $this->assertInstanceOf(LogicException::class, $exception);
        $this->assertInstanceOf(TranslatableInterface::class, $exception);
        $this->assertSame('Routing context not set.', $exception->getMessage());
    }

    private function extension(string $pathInfo = '/docs/intro', string $baseUrl = ''): RoutingExtension
    {
        $router = new Router();
        $router->addRoute('home', '/', 'handler');
        $router->addRoute('docs', '/docs', 'handler');
        $router->addRoute('post', '/blog/{slug}', 'handler');
        $router->setContext(new RequestContext(baseUrl: $baseUrl, host: 'example.com', scheme: 'https', pathInfo: $pathInfo));

        return new RoutingExtension($router);
    }

    public function testItProvidesTheFunctionsOfTheTemplates(): void
    {
        $names = array_map(fn (TwigFunction $function) => $function->getName(), $this->extension()->getFunctions());

        $this->assertSame(['path', 'url', 'is_active_path'], $names);
    }

    public function testThePathAndTheUrlOfARoute(): void
    {
        $extension = $this->extension();

        $this->assertSame('/docs', $extension->getPath('docs'));
        $this->assertSame('/blog/hello', $extension->getPath('post', ['slug' => 'hello']));
        $this->assertSame('https://example.com/docs', $extension->getUrl('docs'));
        $this->assertSame('//example.com/docs', $extension->getUrl('docs', [], true));
    }

    public function testTheFunctionsWorkInTheTemplates(): void
    {
        $twig = new Environment(new ArrayLoader([
            't' => "{{ path('docs') }} {{ url('docs') }} {{ is_active_path('docs') ? 'yes' : 'no' }}",
        ]));
        $twig->addExtension($this->extension());

        $this->assertSame('/docs https://example.com/docs yes', $twig->render('t'));
    }

    public function testAPathIsActiveWhenItIsThePrefixOfTheCurrentOne(): void
    {
        $extension = $this->extension('/docs/intro');

        $this->assertTrue($extension->isActivePath('docs'));
        $this->assertFalse($extension->isActivePath('docs', [], false));
        $this->assertFalse($extension->isActivePath('home', [], false));
        $this->assertFalse($extension->isActivePath('post', ['slug' => 'x']));
    }

    public function testAPathIsActiveWhenItIsTheCurrentOneWithOrWithoutTheSlash(): void
    {
        $this->assertTrue($this->extension('/docs')->isActivePath('docs', [], false));
        $this->assertTrue($this->extension('/docs/')->isActivePath('docs', [], false));
        $this->assertTrue($this->extension('/blog/hello')->isActivePath('post', ['slug' => 'hello'], false));
    }

    public function testTheBaseUrlOfTheContextCountsInTheCurrentPath(): void
    {
        $extension = $this->extension('/docs', '/app');

        $this->assertFalse($extension->isActivePath('docs', [], false));
    }
}
