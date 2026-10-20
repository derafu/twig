<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Middleware;

use Derafu\Twig\Exception\TwigException;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\ServerRequestInterface as PsrRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Adds the template source code to the request for using in the same template.
 */
class SetTemplateCodeMiddleware implements MiddlewareInterface
{
    /**
     * The attribute name used to store the template source code.
     */
    public const TEMPLATE_ATTRIBUTE = 'derafu.template_source_code';

    /**
     * Creates a new SetTemplateCodeMiddleware.
     *
     * @param ParameterBagInterface $params
     */
    public function __construct(private readonly ParameterBagInterface $params)
    {
    }

    /**
     * Processes an incoming server request.
     *
     * @param PsrRequestInterface $request The PSR-7 request.
     * @param RequestHandlerInterface $handler The request handler.
     * @return PsrResponseInterface The response.
     */
    public function process(
        PsrRequestInterface $request,
        RequestHandlerInterface $handler
    ): PsrResponseInterface {
        // Store template source code.
        $path = $request->getUri()->getPath();
        if (str_starts_with($path, '/components/')) {
            $projectDir = $this->params->get('kernel.project_dir');
            if (!is_string($projectDir)) {
                throw new TwigException([
                    'The parameter {parameter} must be a string.',
                    'parameter' => 'kernel.project_dir',
                ]);
            }
            $file = $projectDir . '/templates/pages' . $path . '.html.twig';
            $code = file_get_contents($file);
            $code = '{# derafu_twig:templates/pages' . $path . '.html.twig #}'
                . "\n\n" . $code
            ;

            return $handler->handle(
                $request->withAttribute(
                    self::TEMPLATE_ATTRIBUTE,
                    $code
                )
            );
        }

        return $handler->handle($request);
    }
}
