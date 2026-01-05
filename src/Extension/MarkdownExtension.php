<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Extension;

use Derafu\Markdown\Contract\MarkdownServiceInterface;
use Derafu\Markdown\Service\MarkdownService;
use LogicException;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Twig extension to enable Markdown rendering within templates.
 *
 * This extension provides a `markdown` filter to convert Markdown content
 * into HTML using Derafu: Markdown.
 */
class MarkdownExtension extends AbstractExtension
{
    /**
     * Markdown rendering service.
     *
     * @var MarkdownServiceInterface
     */
    private MarkdownServiceInterface $markdownService;

    /**
     * Constructor.
     *
     * @param MarkdownService|null $markdownService Service for rendering Markdown.
     */
    public function __construct(
        ?MarkdownServiceInterface $markdownService = null
    ) {
        if ($markdownService === null) {
            if (class_exists(MarkdownService::class)) {
                $markdownService = new MarkdownService();
            }
        }

        if ($markdownService !== null) {
            $this->markdownService = $markdownService;
        }
    }

    /**
     * Registers custom Twig filters.
     *
     * @return array List of filters provided by this extension.
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown', [$this, 'renderMarkdown'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
                'needs_context' => true,
            ]),
        ];
    }

    /**
     * Renders Markdown content into HTML.
     *
     * @param Environment $twig Twig environment to render the Twig content
     * if it is included in the Markdown content.
     * @param array $context Context to pass to the Twig template.
     * @param string $content The Markdown content to convert.
     * @param bool $hasTwig Whether the content includes Twig.
     * @return string The rendered HTML.
     */
    public function renderMarkdown(
        Environment $twig,
        array $context,
        string $content,
        bool $hasTwig = false
    ): string {
        // If the Markdown service is not available, throw an exception.
        if (!isset($this->markdownService)) {
            throw new LogicException(
                'MarkdownExtension requires Derafu\\Markdown to be installed. Run "composer require derafu/markdown" to enable this extension.'
            );
        }

        // Protect Twig blocks from being altered by the Markdown parser.
        if ($hasTwig) {
            $twigBlocks = [];
            $content = $this->protectTwigBlocks($content, $twigBlocks);
        }

        // Render the Markdown content.
        $html = $this->markdownService->renderFromString($content);

        // If the content does not include Twig, return just the HTML from the
        // Markdown.
        if (!$hasTwig) {
            return $html;
        }

        // Render the Twig content.
        $html = $this->restoreTwigBlocks($html, $twigBlocks);
        $template = $twig->createTemplate($html);
        return $template->render($context);
    }

    /**
     * Protects Twig-like custom tags from being altered by the Markdown parser.
     *
     * This method temporarily replaces `<twig:... />` self-closing tags with
     * safe HTML comment placeholders so that League\CommonMark does not escape
     * or normalize them during Markdown rendering.
     *
     * The original Twig tags are stored and can be restored later using
     * {@see restoreTwigBlocks()}.
     *
     * This approach allows Twig components to be embedded inside Markdown
     * content while preserving their exact syntax for a later Twig render pass.
     *
     * @param string $content The raw Markdown content.
     * @param array  $store Reference array used to store the original Twig
     * tags, indexed by their generated placeholder.
     *
     * @return string The Markdown content with Twig tags safely replaced.
     */

    private function protectTwigBlocks(string $content, array &$store): string
    {
        return preg_replace_callback(
            '#<twig:(?:[^>"\']+|"[^"]*"|\'[^\']*\')*/>#',
            function ($m) use (&$store) {
                $key = '<!--TWIG_BLOCK_' . count($store) . '-->';
                $store[$key] = $m[0];
                return $key;
            },
            $content
        );
    }

    /**
     * Restores Twig-like custom tags that were protected by
     * {@see protectTwigBlocks()}.
     *
     * This method replaces the HTML comment placeholders with the original
     * Twig tags, allowing the Twig rendering to be applied to the content.
     *
     * @param string $content The Markdown content with Twig tags safely replaced.
     * @param array $store Reference array used to store the original Twig tags,
     * indexed by their generated placeholder.
     *
     * @return string The Markdown content with Twig tags restored.
     */
    private function restoreTwigBlocks(string $content, array $store): string
    {
        return strtr($content, $store);
    }
}
