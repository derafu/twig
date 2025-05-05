<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.org>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Extension;

use Derafu\Routing\Contract\RouterInterface;
use Derafu\Routing\Enum\UrlReferenceType;
use Derafu\Routing\Exception\RouteNotFoundException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension to enable route generation within templates.
 *
 * This extension provides `path` and `url` functions to generate URLs for named
 * routes within Twig templates.
 */
class RoutingExtension extends AbstractExtension
{
    /**
     * Constructor.
     *
     * @param RouterInterface $router The router service.
     */
    public function __construct(private RouterInterface $router)
    {
    }

    /**
     * Registers custom Twig functions.
     *
     * @return array List of functions provided by this extension.
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('path', [$this, 'getPath']),
            new TwigFunction('url', [$this, 'getUrl']),
        ];
    }

    /**
     * Generates a relative path for a route.
     *
     * @param string $name The name of the route
     * @param array $parameters Parameters to pass to the route
     * @return string The generated URL
     * @throws RouteNotFoundException If the route doesn't exist
     */
    public function getPath(string $name, array $parameters = []): string
    {
        return $this->router->generate(
            $name,
            $parameters,
            UrlReferenceType::ABSOLUTE_PATH
        );
    }

    /**
     * Generates an absolute URL for a route.
     *
     * @param string $name The name of the route
     * @param array $parameters Parameters to pass to the route
     * @param bool $schemeRelative Whether to make the URL scheme-relative
     * @return string The generated absolute URL
     * @throws RouteNotFoundException If the route doesn't exist
     */
    public function getUrl(
        string $name,
        array $parameters = [],
        bool $schemeRelative = false
    ): string {
        if ($schemeRelative) {
            return $this->router->generate(
                $name,
                $parameters,
                UrlReferenceType::NETWORK_PATH
            );
        }

        return $this->router->generate(
            $name,
            $parameters,
            UrlReferenceType::ABSOLUTE_URL
        );
    }
}
