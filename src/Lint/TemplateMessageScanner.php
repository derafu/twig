<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\Twig\Lint;

use Derafu\Translation\Exception\Core\TranslatableLogicException as LogicException;
use Derafu\Translation\Lint\MessageReference;
use Derafu\Translation\TranslatableMessage;
use Derafu\Twig\Extension\TranslationExtension;
use Derafu\Twig\Node\TransNode;
use Twig\Environment;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\FilterExpression;
use Twig\Node\Expression\FunctionExpression;
use Twig\Node\Node;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Finds the messages that the templates translate: the `trans` filter, the
 * `t()` function and the `{% trans %}` tag.
 *
 * It reads what Twig itself parses, with the environment the templates are
 * written for (its functions, tags and components), so a comment, a text or a
 * string that only looks like a call is never taken for one. It tells the calls
 * apart by what they are (they belong to the translation extension) and not by
 * their name. Nothing is rendered.
 *
 * The domain is the one the template sets with `{% trans_default_domain %}`, or
 * the one the call gives. The `t()` function does not use the default domain of
 * the template, as it does not when the template is rendered.
 *
 * It finds the messages as the same references that `Derafu\Translation\Lint`
 * finds in the code, so they can be checked in the same way. It only finds
 * them: whether each one has its translation is for whoever uses it to decide,
 * with the catalogues of the templates that were scanned.
 *
 * Part of the lint tools: it is for tools and tests, never for the code that
 * runs the package.
 */
final class TemplateMessageScanner
{
    /**
     * @param Environment $twig The environment the templates are written for.
     * It must have the translation extension.
     * @param string|null $defaultDomain The domain of the messages that do not
     * have one: the one of the translation extension of the environment, or
     * `null` when it has none (the domain is then `messages`, the default one of
     * `TranslatableMessage`).
     */
    public function __construct(
        private readonly Environment $twig,
        private readonly ?string $defaultDomain = null
    ) {
    }

    /**
     * Finds the messages of a template, in order of line.
     *
     * @param string $name Name of the template, as the loader knows it.
     * @return list<MessageReference>
     * @throws LogicException If the environment has no translation extension.
     * @throws \Twig\Error\Error If the template can not be loaded or parsed with
     * this environment. It is not hidden: a template that can not be read would
     * look like one with no messages.
     */
    public function scanTemplate(string $name): array
    {
        if (!$this->twig->hasExtension(TranslationExtension::class)) {
            throw new LogicException([
                'The Twig environment has no translation extension: register the {extension}.',
                'extension' => TranslationExtension::class,
            ]);
        }

        $template = TemplateSource::load($this->twig, $name);

        $found = [];
        $this->collect($template->ast, $template, $found);

        $references = array_values($found);
        usort($references, fn (MessageReference $a, MessageReference $b) => $a->line <=> $b->line);

        return $references;
    }

    /**
     * Finds the messages of every template of a directory, in order of template
     * and then of line.
     *
     * The directory must be one of the paths of the loader of the environment,
     * because the templates are loaded by their name relative to it.
     *
     * @param string $directory Directory with the templates.
     * @param string $extension Extension of the files that are templates.
     * @return list<MessageReference>
     */
    public function scanDirectory(string $directory, string $extension = 'twig'): array
    {
        $references = [];
        foreach ((new TemplateFinder())->find($directory, $extension) as $template) {
            array_push($references, ...$this->scanTemplate($template));
        }

        return $references;
    }

    /**
     * @param array<string, MessageReference> $references
     */
    private function collect(Node $node, TemplateSource $template, array &$references): void
    {
        $reference = match (true) {
            $node instanceof FilterExpression && $this->isFilter($node) => $this->isTranslatedFunction($node)
                ? null
                : $this->fromFilter($node),
            $node instanceof FunctionExpression && $this->isFunction($node) => $this->fromFunction($node),
            $node instanceof TransNode => $this->fromTag($node),
            default => null,
        };

        if ($reference !== null) {
            [$id, $domain] = $reference;
            $parsedLine = $node->getTemplateLine();
            $dynamic = $id === null || $domain === null;

            // Twig can have more than one node for what the template writes
            // once (it copies the node of a filter that it uses twice): it is
            // the same message, in the same place.
            $references[$parsedLine . "\0" . $id . "\0" . $domain] ??= new MessageReference(
                $id,
                $domain,
                TranslatableMessage::class,
                $template->file,
                $template->line($parsedLine),
                $dynamic ? $template->name : null,
                $dynamic ? $template->text($parsedLine) : null
            );
        }

        foreach ($node as $child) {
            $this->collect($child, $template, $references);
        }
    }

    private function isFilter(FilterExpression $node): bool
    {
        $filter = $node->hasAttribute('twig_callable') ? $node->getAttribute('twig_callable') : null;

        return $filter instanceof TwigFilter && $this->isTranslationCallable($filter->getCallable(), 'trans');
    }

    /**
     * Whether a `trans` filter translates what `t()` made, as in
     * `t('Close')|trans`. Its message is the one of `t()`, which is found as the
     * call that it is: the filter is not another message.
     */
    private function isTranslatedFunction(FilterExpression $node): bool
    {
        $input = $node->getNode('node');

        return $input instanceof FunctionExpression && $this->isFunction($input);
    }

    private function isFunction(FunctionExpression $node): bool
    {
        $function = $node->hasAttribute('twig_callable') ? $node->getAttribute('twig_callable') : null;

        return $function instanceof TwigFunction && $this->isTranslationCallable($function->getCallable(), 'createTranslatable');
    }

    private function isTranslationCallable(mixed $callable, string $method): bool
    {
        return is_array($callable) && $callable[0] instanceof TranslationExtension && $callable[1] === $method;
    }

    /**
     * The message and the domain of `message|trans(parameters, domain, locale)`.
     * The domain is the one the call gives, or the default one of the template
     * (Twig already put it in the call when the template was parsed).
     *
     * @return array{string|null, string|null}
     */
    private function fromFilter(FilterExpression $node): array
    {
        $arguments = $node->getNode('arguments');

        return [
            $this->literal($node->getNode('node')),
            $this->domain($this->argument($arguments, 1, 'domain')),
        ];
    }

    /**
     * The message and the domain of `t(message, parameters, domain, locale)`.
     *
     * @return array{string|null, string|null}
     */
    private function fromFunction(FunctionExpression $node): array
    {
        $arguments = $node->getNode('arguments');
        $message = $this->argument($arguments, 0, 'message');

        return [
            $message === null ? null : $this->literal($message),
            $this->domain($this->argument($arguments, 2, 'domain')),
        ];
    }

    /**
     * The message and the domain of `{% trans %}message{% endtrans %}`. The
     * message is always the text of the tag.
     *
     * @return array{string|null, string|null}
     */
    private function fromTag(TransNode $node): array
    {
        return [
            $node->getAttribute('message'),
            $node->hasNode('domain') ? $this->domain($node->getNode('domain')) : $this->defaultDomain ?? 'messages',
        ];
    }

    /**
     * An argument of a call, by its position or by its name.
     */
    private function argument(Node $arguments, int $position, string $name): ?Node
    {
        return match (true) {
            $arguments->hasNode((string) $position) => $arguments->getNode((string) $position),
            $arguments->hasNode($name) => $arguments->getNode($name),
            default => null,
        };
    }

    /**
     * The text of an expression, when it is written as a literal text.
     */
    private function literal(Node $node): ?string
    {
        return $node instanceof ConstantExpression && is_string($node->getAttribute('value'))
            ? $node->getAttribute('value')
            : null;
    }

    /**
     * The domain of a call: the one it gives, written as a literal, or the
     * default one if it gives none. `null` if it gives one that is not a
     * literal, so it is only known when the template is rendered.
     */
    private function domain(?Node $node): ?string
    {
        if ($node === null) {
            return $this->defaultDomain ?? 'messages';
        }

        if ($node instanceof ConstantExpression && $node->getAttribute('value') === null) {
            return $this->defaultDomain ?? 'messages';
        }

        return $this->literal($node);
    }
}
