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

use Derafu\TestsTwig\Fixture\GreetingExtension;
use Derafu\Translation\Exception\Logic\TranslatableInvalidArgumentException;
use Derafu\Translation\TranslatableMessage;
use Derafu\Twig\Contract\TwigServiceInterface;
use Derafu\Twig\Exception\TwigException;
use Derafu\Twig\Extension\TranslationExtension;
use Derafu\Twig\Provider\DirectoryComponentProvider;
use Derafu\Twig\Translation\TwigTranslationResourceProvider;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * Imports the actual package configuration and checks its provider both as a
 * service and through the translator of a consuming application.
 */
#[CoversNothing]
final class ServicesTest extends TestCase
{
    private function loadContainer(?string $consumer = null): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $root = dirname(__DIR__, 2);
        $container->setParameter('fixture.templates', $root . '/tests/fixtures/services');
        $container->setParameter('fixture.components', $root . '/tests/fixtures/templates');
        $container->setParameter('fixture.isolated_templates', $root . '/tests/fixtures/services/isolated');
        (new YamlFileLoader($container, new FileLocator()))->load(
            $consumer === null ? $root . '/resources/config/twig-services.yaml' : $root . '/tests/fixtures/config/' . $consumer
        );

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
        $container = $this->loadContainer('translation.yaml');
        $this->compile($container);
        $translator = $container->get(TranslatorInterface::class);
        self::assertInstanceOf(Translator::class, $translator);

        return $translator;
    }

    public function testTheDefaultYamlProvidesAWorkingTwigService(): void
    {
        $container = $this->loadContainer();
        $this->compile($container);
        $service = $container->get(TwigServiceInterface::class);
        self::assertInstanceOf(TwigServiceInterface::class, $service);
        $data = ['name' => 'Ada'];
        self::assertSame('Hello Ada', $service->renderFromString('Hello {{ name }}', $data));
    }

    public function testTaggedProvidersReachTheEnvironmentUsedToRenderTemplates(): void
    {
        $container = $this->loadContainer('consumer.yaml');
        $this->compile($container);
        $service = $container->get(TwigServiceInterface::class);
        self::assertInstanceOf(TwigServiceInterface::class, $service);
        $data = ['name' => 'Ada'];
        self::assertSame("Hello Ada\n", $service->render('greeting', $data));
        $html = $service->render('component');
        self::assertStringContainsString('From provider', $html);
        self::assertStringContainsString('success', $html);
        self::assertSame((string) spl_object_id($service->getTwig()), trim($service->render('identity')));
    }

    public function testIndividuallyTaggedComponentsAreAdaptedToAProvider(): void
    {
        $container = $this->loadContainer('tagged-components.yaml');
        $this->compile($container);
        $service = $container->get(TwigServiceInterface::class);
        self::assertInstanceOf(TwigServiceInterface::class, $service);
        $html = $service->render('component');
        self::assertStringContainsString('From provider', $html);
        self::assertStringContainsString('alert-success', $html);
    }

    public function testProviderCyclesRemainLazyInACompiledContainer(): void
    {
        $container = $this->loadContainer('consumer.yaml');
        $this->compile($container);
        $class = 'TwigProviderServicesTestContainer';
        $code = '';
        if (!class_exists($class, false)) {
            $dump = (new PhpDumper($container))->dump(['class' => $class]);
            self::assertIsString($dump);
            $code = substr($dump, 5);
        }
        $compiled = eval($code . "\nreturn new " . $class . '();');
        self::assertInstanceOf(ContainerInterface::class, $compiled);
        $service = $compiled->get(TwigServiceInterface::class);
        self::assertInstanceOf(TwigServiceInterface::class, $service);
        self::assertSame((string) spl_object_id($service->getTwig()), trim($service->render('identity')));
    }

    public function testAnExistingEnvironmentIsPreservedByTheYamlOverride(): void
    {
        $container = $this->loadContainer('existing-environment.yaml');
        $this->compile($container);
        $environment = $container->get(Environment::class);
        $service = $container->get(TwigServiceInterface::class);
        self::assertInstanceOf(TwigServiceInterface::class, $service);
        self::assertSame($environment, $service->getTwig());
        self::assertTrue($service->getTwig()->isStrictVariables());
        self::assertFalse($service->getTwig()->hasExtension(TranslationExtension::class));
        self::assertSame('Native preserved', $service->render('native'));
    }

    public function testAnIsolatedEnvironmentDoesNotReplaceTheDefaultOne(): void
    {
        $container = $this->loadContainer('isolated.yaml');
        $this->compile($container);
        $default = $container->get(TwigServiceInterface::class);
        $isolated = $container->get(TwigServiceInterface::class . ' $isolated');
        self::assertInstanceOf(TwigServiceInterface::class, $default);
        self::assertInstanceOf(TwigServiceInterface::class, $isolated);
        self::assertNotSame($default->getTwig(), $isolated->getTwig());
        self::assertSame("default\n", $default->render('source'));
        self::assertSame("isolated\n", $isolated->render('source'));
        self::assertFalse($isolated->getTwig()->hasExtension(GreetingExtension::class));
        self::assertSame('Hello Ada', $default->renderFromString('{{ "Ada"|greet }}'));
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
