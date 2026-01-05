<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Service;

use Derafu\Twig\Contract\TwigCreatorInterface;
use Derafu\Twig\Contract\TwigServiceInterface;
use Derafu\Twig\Provider\AllComponentProvider;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigService implements TwigServiceInterface
{
    /**
     * HTML template renderer with Twig.
     *
     * @var Environment
     */
    private Environment $twig;

    /**
     * Twig instance creator.
     *
     * @var TwigCreatorInterface
     */
    private TwigCreatorInterface $twigCreator;

    /**
     * Service constructor.
     *
     * @param Environment|TwigCreatorInterface|array|null $twig
     */
    public function __construct(
        Environment|TwigCreatorInterface|array|null $twig = null,
    ) {
        if (is_array($twig)) {
            $this->twigCreator = new TwigCreator($twig);
        } elseif ($twig instanceof TwigCreatorInterface) {
            $this->twigCreator = $twig;
        } elseif ($twig === null) {
            $this->twigCreator = new TwigCreator();
        } else {
            $this->twig = $twig;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render(string $template, array &$data = []): string
    {
        //$config = $data['options']['config']['html'] ?? [];

        // Resolve template.
        $template = $this->resolveTemplate($template);

        // Render the template.
        return $this->getTwig()->render($template, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function getTwig(): Environment
    {
        if (!isset($this->twig)) {
            $this->twig = $this->twigCreator->create([
                'components' => new AllComponentProvider(),
            ]);
        }

        return $this->twig;
    }

    /**
     * Resolves the requested template.
     *
     * Responsible for:
     *
     *   - Adding the extension to the template.
     *   - Adding the directory if an absolute path was provided.
     *
     * @param string $template
     * @return string
     */
    private function resolveTemplate(string $template): string
    {
        // Add extension if necessary.
        if (!str_ends_with($template, '.twig')) {
            $template .= '.html.twig';
        }

        // Get the loader.
        $loader = $this->getTwig()->getLoader();
        if (!$loader instanceof FilesystemLoader) {
            return $template;
        }

        // Add the directory if an absolute path was provided.
        $realpath = realpath($template);
        if ($realpath) {
            $dir = dirname($realpath);
            if (!in_array($dir, $loader->getPaths())) {
                $loader->addPath($dir);
            }
            $template = basename($realpath);
        }

        // Return template name.
        return $template;
    }
}
