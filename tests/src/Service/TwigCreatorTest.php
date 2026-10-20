<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Service;

use Derafu\TestsTwig\Fixture\Component\AlertComponent;
use Derafu\Twig\Cache\CacheItemPool;
use Derafu\Twig\Contract\ComponentRegistrarInterface;
use Derafu\Twig\Extension\MarkdownExtension;
use Derafu\Twig\Extension\TranslationExtension;
use Derafu\Twig\Extension\TwigExtension;
use Derafu\Twig\Service\ComponentRegistrar;
use Derafu\Twig\Service\TwigCreator;
use Derafu\Twig\Support\ProjectDirectory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Extra\CssInliner\CssInlinerExtension;
use Twig\Extra\Inky\InkyExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\Extra\String\StringExtension;
use Twig\Loader\FilesystemLoader;

/**
 * Creates the Twig environment from its options.
 */
#[CoversClass(TwigCreator::class)]
#[UsesClass(TranslationExtension::class)]
#[UsesClass(MarkdownExtension::class)]
#[UsesClass(TwigExtension::class)]
#[UsesClass(ComponentRegistrar::class)]
#[UsesClass(CacheItemPool::class)]
#[UsesClass(ProjectDirectory::class)]
final class TwigCreatorTest extends TestCase
{
    /**
     * @return array<string>
     */
    private function paths(Environment $twig): array
    {
        $loader = $twig->getLoader();
        $this->assertInstanceOf(FilesystemLoader::class, $loader);

        return $loader->getPaths();
    }

    public function testTheOptionsOfTheConstructorAreTheOnesOfEveryEnvironmentUnlessTheyAreChanged(): void
    {
        $creator = new TwigCreator(['paths' => __DIR__, 'extra' => false]);

        $this->assertSame([__DIR__], $this->paths($creator->create()));
        $this->assertSame([dirname(__DIR__)], $this->paths($creator->create(['paths' => dirname(__DIR__)])));
        $this->assertSame([__DIR__, dirname(__DIR__)], $this->paths($creator->create(['paths' => [__DIR__, dirname(__DIR__)]])));
        $this->assertFalse($creator->create()->hasExtension(MarkdownExtension::class));
    }

    public function testWithoutPathsThereAreNoneWhenTheProjectHasNoTemplates(): void
    {
        $twig = (new TwigCreator())->create(['extra' => false]);

        $this->assertSame([], $this->paths($twig));
    }

    public function testWithoutPathsTheTemplatesOfTheProjectAreTheOnesThatAreUsed(): void
    {
        $templates = ProjectDirectory::path() . '/templates';
        if (file_exists($templates)) {
            $this->markTestSkipped('The project has its own templates directory.');
        }

        mkdir($templates);
        try {
            $twig = (new TwigCreator())->create(['extra' => false]);

            $this->assertSame([$templates], $this->paths($twig));
        } finally {
            rmdir($templates);
        }
    }

    public function testTheExtraExtensionsAreThereWhenTheyAreInstalled(): void
    {
        $twig = (new TwigCreator())->create(['paths' => __DIR__]);

        foreach ([
            MarkdownExtension::class,
            CssInlinerExtension::class,
            IntlExtension::class,
            StringExtension::class,
            InkyExtension::class,
        ] as $extension) {
            $this->assertTrue($twig->hasExtension($extension), $extension);
        }
    }

    public function testTheExtensionsThatAreGivenAreAdded(): void
    {
        $twig = (new TwigCreator())->create(['paths' => __DIR__, 'extra' => false, 'extensions' => [new TwigExtension()]]);

        $this->assertTrue($twig->hasExtension(TwigExtension::class));
        $this->assertTrue($twig->hasExtension(TranslationExtension::class));
    }

    public function testTheComponentsAreRegisteredByTheRegistrar(): void
    {
        $registrar = $this->createMock(ComponentRegistrarInterface::class);
        $registrar->expects($this->once())
            ->method('register')
            ->with($this->isInstanceOf(Environment::class), [AlertComponent::class])
            ->willReturnArgument(0);

        (new TwigCreator([], $registrar))->create(['paths' => __DIR__, 'extra' => false, 'components' => [AlertComponent::class]]);
    }

    public function testWithoutComponentsTheRegistrarIsNotUsed(): void
    {
        $registrar = $this->createMock(ComponentRegistrarInterface::class);
        $registrar->expects($this->never())->method('register');

        (new TwigCreator([], $registrar))->create(['paths' => __DIR__, 'extra' => false]);
    }
}
