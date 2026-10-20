<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Middleware;

use Derafu\Translation\Contract\TranslatableInterface;
use Derafu\Twig\Exception\TwigException;
use Derafu\Twig\Middleware\SetTemplateCodeMiddleware;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Throwable;

/**
 * The source of the template of the pages of the components goes in the request.
 */
#[CoversClass(SetTemplateCodeMiddleware::class)]
final class SetTemplateCodeMiddlewareTest extends TestCase
{
    private string $projectDir;

    protected function setUp(): void
    {
        $this->projectDir = sys_get_temp_dir() . '/derafu-twig-middleware-' . bin2hex(random_bytes(4));
        mkdir($this->projectDir . '/templates/pages/components', 0777, true);
        file_put_contents($this->projectDir . '/templates/pages/components/button.html.twig', '<button />');
    }

    protected function tearDown(): void
    {
        unlink($this->projectDir . '/templates/pages/components/button.html.twig');
        rmdir($this->projectDir . '/templates/pages/components');
        rmdir($this->projectDir . '/templates/pages');
        rmdir($this->projectDir . '/templates');
        rmdir($this->projectDir);
    }

    private function uri(string $path): UriInterface
    {
        $uri = $this->createStub(UriInterface::class);
        $uri->method('getPath')->willReturn($path);

        return $uri;
    }

    private function request(string $path): ServerRequestInterface
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($this->uri($path));

        return $request;
    }

    private function requestMock(string $path): ServerRequestInterface&MockObject
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($this->uri($path));

        return $request;
    }

    public function testARequestOfOtherPageGoesOnWithoutChanges(): void
    {
        $request = $this->requestMock('/about');
        $request->expects($this->never())->method('withAttribute');
        $response = $this->createStub(ResponseInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())->method('handle')->with($this->identicalTo($request))->willReturn($response);

        $middleware = new SetTemplateCodeMiddleware(new ParameterBag(['kernel.project_dir' => $this->projectDir]));

        $this->assertSame($response, $middleware->process($request, $handler));
    }

    public function testTheSourceOfTheTemplateOfAComponentPageGoesInTheRequest(): void
    {
        $modified = $this->createStub(ServerRequestInterface::class);
        $request = $this->requestMock('/components/button');
        $request->expects($this->once())
            ->method('withAttribute')
            ->with(
                SetTemplateCodeMiddleware::TEMPLATE_ATTRIBUTE,
                "{# derafu_twig:templates/pages/components/button.html.twig #}\n\n<button />"
            )
            ->willReturn($modified);
        $response = $this->createStub(ResponseInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())->method('handle')->with($this->identicalTo($modified))->willReturn($response);

        $middleware = new SetTemplateCodeMiddleware(new ParameterBag(['kernel.project_dir' => $this->projectDir]));

        $this->assertSame($response, $middleware->process($request, $handler));
    }

    public function testAProjectDirectoryThatIsNotAStringIsATranslatableError(): void
    {
        $middleware = new SetTemplateCodeMiddleware(new ParameterBag(['kernel.project_dir' => ['not', 'a', 'string']]));

        $exception = null;
        try {
            $middleware->process($this->request('/components/button'), $this->createStub(RequestHandlerInterface::class));
        } catch (Throwable $e) {
            $exception = $e;
        }

        $this->assertInstanceOf(TwigException::class, $exception);
        $this->assertInstanceOf(TranslatableInterface::class, $exception);
        $this->assertSame('The parameter kernel.project_dir must be a string.', $exception->getMessage());
    }
}
