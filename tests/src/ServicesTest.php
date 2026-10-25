<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig;

use Derafu\Translation\Exception\Logic\TranslatableInvalidArgumentException;
use Derafu\Translation\TranslatableMessage;
use Derafu\Translation\TranslatorFactory;
use Derafu\Twig\Exception\TwigException;
use Derafu\Twig\Extension\TranslationExtension;
use Derafu\Twig\Provider\DirectoryComponentProvider;
use Derafu\Twig\Translation\TwigTranslationResourceProvider;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * Imports the actual package configuration and checks its provider both as a
 * service and through the translator of a consuming application.
 */
#[CoversNothing]
final class ServicesTest extends TestCase
{
    private function loadContainer(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        (new YamlFileLoader($container, new FileLocator(dirname(__DIR__, 2) . '/resources/config')))
            ->load('twig-services.yaml');

        return $container;
    }

    private function compile(ContainerBuilder $container): void
    {
        // Keep package services reachable so unused-private-service removal
        // cannot hide an invalid definition from the construction checks.
        foreach ($container->getDefinitions() as $definition) {
            $definition->setPublic(true);
        }
        foreach ($container->getAliases() as $alias) {
            $alias->setPublic(true);
        }
        $container->compile();
    }

    private function consumerTranslator(): Translator
    {
        $container = $this->loadContainer();
        // The application chooses its own translator and locale. The package
        // only contributes its resource provider through the declared tag.
        $container->register('app.translator', Translator::class)
            ->setFactory([TranslatorFactory::class, 'create'])
            ->setArguments(['es', ['en'], new TaggedIteratorArgument('derafu_translation.resource_provider')]);
        $this->compile($container);
        $translator = $container->get('app.translator');
        self::assertInstanceOf(Translator::class, $translator);

        return $translator;
    }

    public function testEveryConfiguredServiceCanBeConstructed(): void
    {
        $container = $this->loadContainer();
        $this->compile($container);
        self::assertInstanceOf(TwigTranslationResourceProvider::class, $container->get(TwigTranslationResourceProvider::class));
    }

    public function testTheTaggedProviderExposesThePackageCatalogue(): void
    {
        $container = $this->loadContainer();
        self::assertArrayHasKey(
            TwigTranslationResourceProvider::class,
            $container->findTaggedServiceIds('derafu_translation.resource_provider')
        );
        $this->compile($container);
        $provider = $container->get(TwigTranslationResourceProvider::class);
        self::assertInstanceOf(TwigTranslationResourceProvider::class, $provider);
        $directories = [...$provider->getDirectories()];
        self::assertCount(1, $directories);
        self::assertSame(realpath(dirname(__DIR__, 2) . '/resources/translations'), realpath($directories[0]));
        self::assertFileExists($directories[0] . '/errors+intl-icu.es.php');
    }

    public function testTaggedResourcesTranslateARealProviderErrorInATwigTemplate(): void
    {
        $translator = $this->consumerTranslator();
        $twig = new Environment(new ArrayLoader(['error' => '{{ error|trans }}']), ['autoescape' => false]);
        $twig->addExtension(new TranslationExtension($translator));
        $directory = __DIR__ . '/../fixtures/missing-services-directory';
        self::assertDirectoryDoesNotExist($directory);
        try {
            new DirectoryComponentProvider($directory);
            self::fail('A missing component directory was accepted.');
        } catch (TranslatableInvalidArgumentException $exception) {
            self::assertSame(
                'El directorio "' . $directory . '" no existe.',
                $twig->render('error', ['error' => $exception])
            );
        }
    }

    public function testTaggedResourcesTranslateARealTranslationExtensionError(): void
    {
        $translator = $this->consumerTranslator();
        $extension = new TranslationExtension($translator);
        try {
            $extension->trans(new TranslatableMessage('Example'), ['unexpected' => 'value']);
            self::fail('Extra parameters for an already translatable message were accepted.');
        } catch (TwigException $exception) {
            self::assertSame(
                'El filtro "trans" no recibe parámetros para un mensaje que ya es traducible: son los del mensaje.',
                $exception->trans($translator)
            );
        }
    }
}
