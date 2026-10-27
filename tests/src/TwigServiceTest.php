<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig;

use Derafu\Translation\TranslatorFactory;
use Derafu\Twig\Cache\CacheItemPool;
use Derafu\Twig\Contract\TwigCreatorInterface;
use Derafu\Twig\Extension\MarkdownExtension;
use Derafu\Twig\Extension\TranslationExtension;
use Derafu\Twig\NodeVisitor\TranslationDefaultDomainNodeVisitor;
use Derafu\Twig\Provider\DefaultExtensionProvider;
use Derafu\Twig\Provider\DirectoryComponentProvider;
use Derafu\Twig\Service\ComponentRegistrar;
use Derafu\Twig\Service\TwigCreator;
use Derafu\Twig\Service\TwigService;
use Derafu\Twig\Support\ProjectDirectory;
use Derafu\Twig\TokenParser\TransDefaultDomainTokenParser;
use Derafu\Twig\TokenParser\TransTokenParser;
use Derafu\Twig\Translation\TwigTranslationResourceProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;

#[CoversClass(TwigService::class)]
#[CoversClass(CacheItemPool::class)]
#[CoversClass(DirectoryComponentProvider::class)]
#[CoversClass(ComponentRegistrar::class)]
#[CoversClass(TwigCreator::class)]
#[UsesClass(TranslationExtension::class)]
#[UsesClass(ProjectDirectory::class)]
#[UsesClass(MarkdownExtension::class)]
#[UsesClass(TwigTranslationResourceProvider::class)]
#[UsesClass(TranslationDefaultDomainNodeVisitor::class)]
#[UsesClass(TransTokenParser::class)]
#[UsesClass(TransDefaultDomainTokenParser::class)]
#[UsesClass(DefaultExtensionProvider::class)]
class TwigServiceTest extends TestCase
{
    public function testRenderFromString(): void
    {
        $twigService = new TwigService(['extra' => false]);

        $template = 'Hello {{ name }}';
        $data = ['name' => 'World'];
        $html = $twigService->renderFromString($template, $data);
        $this->assertSame('Hello World', $html);
    }

    public function testTheTranslationExtensionIsAlwaysRegistered(): void
    {
        $twig = (new TwigCreator())->create(['extra' => false]);

        $this->assertTrue($twig->hasExtension(TranslationExtension::class));
        $this->assertSame('Close', $twig->createTemplate("{{ 'Close'|trans }}")->render());
    }

    public function testTheExtensionOfTheApplicationIsTheOneThatIsUsed(): void
    {
        $translator = TranslatorFactory::create('es', [], [new TwigTranslationResourceProvider()]);
        $extension = new TranslationExtension($translator, 'errors', 'es');

        $twig = (new TwigCreator())->create(['extra' => false, 'extensions' => [$extension]]);

        $this->assertSame($extension, $twig->getExtension(TranslationExtension::class));
        $this->assertSame(
            'El contexto de rutas no está definido.',
            $twig->createTemplate("{{ 'Routing context not set.'|trans }}")->render()
        );
    }

    public function testAnEnvironmentThatIsGivenIsTheOneThatIsUsed(): void
    {
        $twig = new Environment(new ArrayLoader(['page.html.twig' => 'Hello {{ name }}']));
        $service = new TwigService($twig);
        $data = ['name' => 'World'];

        $this->assertSame(spl_object_id($twig), spl_object_id($service->getTwig()));
        $this->assertSame('Hello World', $service->render('page', $data));
    }

    public function testACreatorThatIsGivenCreatesTheEnvironmentOnlyOnce(): void
    {
        $twig = new Environment(new ArrayLoader());
        $creator = $this->createMock(TwigCreatorInterface::class);
        $creator->expects($this->once())->method('create')->willReturn($twig);
        $service = new TwigService($creator);

        $first = $service->getTwig();
        $second = $service->getTwig();

        $this->assertSame(spl_object_id($twig), spl_object_id($first));
        $this->assertSame(spl_object_id($first), spl_object_id($second));
    }

    public function testWithoutAnythingItIsCreatedWithTheDefaultCreator(): void
    {
        $this->assertInstanceOf(Environment::class, (new TwigService())->getTwig());
    }

    public function testATemplateIsFoundInThePathsAndTheExtensionIsAdded(): void
    {
        $directory = $this->directoryWith(['page.html.twig' => 'Hello {{ name }}']);
        $service = new TwigService(['extra' => false, 'paths' => [$directory]]);
        $data = ['name' => 'World'];

        $this->assertSame('Hello World', $service->render('page', $data));
        $this->assertSame('Hello World', $service->render('page.html.twig', $data));
    }

    public function testATemplateOfAnAbsolutePathIsFoundAndItsDirectoryIsAddedOnce(): void
    {
        $directory = $this->directoryWith(['page.html.twig' => 'Hello {{ name }}']);
        $service = new TwigService(['extra' => false, 'paths' => [$this->directoryWith([])]]);
        $data = ['name' => 'World'];

        $this->assertSame('Hello World', $service->render($directory . '/page', $data));
        $this->assertSame('Hello World', $service->render($directory . '/page.html.twig', $data));

        $loader = $service->getTwig()->getLoader();
        $this->assertInstanceOf(FilesystemLoader::class, $loader);
        $this->assertCount(1, array_keys($loader->getPaths(), realpath($directory)));
    }

    public function testWhenTheLoaderIsNotOfFilesTheNameIsTheOneThatWasGiven(): void
    {
        $service = new TwigService(new Environment(new ArrayLoader(['page.html.twig' => 'Hi'])));
        $data = [];

        $this->assertSame('Hi', $service->render('page', $data));
    }

    /**
     * @param array<string,string> $files
     */
    private function directoryWith(array $files): string
    {
        $directory = sys_get_temp_dir() . '/derafu-twig-service-' . bin2hex(random_bytes(4));
        mkdir($directory);
        foreach ($files as $name => $content) {
            file_put_contents($directory . '/' . $name, $content);
        }
        $this->directories[] = $directory;

        return $directory;
    }

    /**
     * @var list<string>
     */
    private array $directories = [];

    protected function tearDown(): void
    {
        foreach ($this->directories as $directory) {
            foreach (scandir($directory) ?: [] as $entry) {
                if ($entry !== '.' && $entry !== '..') {
                    unlink($directory . '/' . $entry);
                }
            }
            rmdir($directory);
        }
    }
}
