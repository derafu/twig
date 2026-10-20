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
use Derafu\TestsTwig\Fixture\Component\BoxComponent;
use Derafu\TestsTwig\Fixture\Component\GreetingComponent;
use Derafu\TestsTwig\Fixture\Component\TagComponent;
use Derafu\TestsTwig\Fixture\Component\UnnamedComponent;
use Derafu\TestsTwig\Fixture\ComponentsTwig;
use Derafu\Twig\Cache\CacheItemPool;
use Derafu\Twig\Provider\ArrayComponentProvider;
use Derafu\Twig\Service\ComponentRegistrar;
use Derafu\Twig\Support\ProjectDirectory;
use FilesystemIterator;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use stdClass;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\UX\TwigComponent\Twig\ComponentRuntime;
use Throwable;
use Twig\Environment;
use Twig\Error\RuntimeError;
use Twig\Loader\FilesystemLoader;

/**
 * The components are registered in a Twig environment, and their tags (`<twig:...>`)
 * render them.
 */
#[CoversClass(ComponentRegistrar::class)]
#[UsesClass(CacheItemPool::class)]
#[UsesClass(ProjectDirectory::class)]
#[UsesClass(ArrayComponentProvider::class)]
final class ComponentRegistrarTest extends TestCase
{
    /**
     * The key of the configuration of some components in the cache: it depends
     * on them, so other components do not use it.
     *
     * @param array<class-string> $components
     */
    private function key(array $components): string
    {
        sort($components);

        return 'derafu_twig_components_config_' . hash('sha256', implode("\n", $components));
    }

    private function environment(): Environment
    {
        return new Environment(new FilesystemLoader([ComponentsTwig::TEMPLATES]));
    }

    /**
     * @param ArrayComponentProvider|iterable<class-string|object> $components
     */
    private function render(string $template, ArrayComponentProvider|iterable $components, ?ArrayAdapter $cache = null): string
    {
        $twig = $this->environment();
        (new ComponentRegistrar($cache ?? new ArrayAdapter()))->register($twig, $components);

        return $twig->createTemplate($template)->render();
    }

    public function testAComponentTagRendersTheComponent(): void
    {
        $html = $this->render('<twig:test-alert content="Hi" type="warning" />', [AlertComponent::class]);

        $this->assertSame('<div class="alert alert-warning">Hi</div>', trim($html));
    }

    public function testTheComponentsCanBeTheirClassesTheirInstancesOrAProvider(): void
    {
        $template = '<twig:test-alert content="Hi" />';
        $expected = '<div class="alert alert-info">Hi</div>';

        $generator = (static function (): Generator {
            yield AlertComponent::class;
        })();

        $this->assertSame($expected, trim($this->render($template, [AlertComponent::class])));
        $this->assertSame($expected, trim($this->render($template, [new AlertComponent()])));
        $this->assertSame($expected, trim($this->render($template, $generator)));
        $this->assertSame($expected, trim($this->render($template, new ArrayComponentProvider([AlertComponent::class]))));
    }

    public function testTheMethodsBeforeAndAfterTheMountAreCalled(): void
    {
        $html = $this->render('<twig:test-greeting name="world" />', [GreetingComponent::class]);

        // The name is changed before it is set, and the greeting is built after.
        $this->assertSame('<p>Hello WORLD</p>', trim($html));
    }

    public function testTheComponentCanHaveItsOwnTemplateAndNameForItsAttributes(): void
    {
        $html = $this->render('<twig:test-box label="Label" class="wide" />', [BoxComponent::class]);

        $this->assertSame('<div class="wide">Label</div>', trim($html));
    }

    public function testTheAttributesOfAComponentAreNotEscaped(): void
    {
        $html = $this->render('<twig:test-tag class="wide" data-x="1" />', [TagComponent::class]);

        $this->assertStringContainsString('class="wide" data-x="1"', $html);
        $this->assertStringNotContainsString('&quot;', $html);
    }

    public function testTheValueOfAnAttributeIsEscapedOnceAndNotTwice(): void
    {
        $html = $this->render("<twig:test-tag title='a \"b\" <c>' />", [TagComponent::class]);

        // ComponentAttributes escapes the value itself: the template must not do
        // it again.
        $this->assertStringContainsString('title="a &quot;b&quot; &lt;c&gt;"', $html);
        $this->assertStringNotContainsString('&amp;', $html);
    }

    public function testTheRuntimeLoaderOnlyKnowsTheRuntimeOfTheComponents(): void
    {
        $twig = $this->environment();
        (new ComponentRegistrar(new ArrayAdapter()))->register($twig, [AlertComponent::class]);

        $this->assertInstanceOf(ComponentRuntime::class, $twig->getRuntime(ComponentRuntime::class));

        $this->expectException(RuntimeError::class);
        $twig->getRuntime(stdClass::class);
    }

    public function testAComponentThatDoesNotSayItsNameHasTheOneOfItsClass(): void
    {
        $html = $this->render('<twig:unnamedcomponent />', [UnnamedComponent::class]);

        $this->assertSame('<i>unnamed</i>', trim($html));
    }

    public function testAClassThatIsNotAComponentIsNotRegistered(): void
    {
        $twig = $this->environment();
        (new ComponentRegistrar(new ArrayAdapter()))->register($twig, [AlertComponent::class, stdClass::class]);

        $this->assertSame(
            '<div class="alert alert-info">Hi</div>',
            trim($twig->createTemplate('<twig:test-alert content="Hi" />')->render())
        );

        $exception = null;
        try {
            $twig->createTemplate('<twig:stdclass />')->render();
        } catch (Throwable $e) {
            $exception = $e;
        }
        $this->assertNotNull($exception);
    }

    public function testTheConfigurationIsReadFromTheCacheTheNextTime(): void
    {
        $cache = new ArrayAdapter();
        $this->render('<twig:test-alert content="Hi" />', [AlertComponent::class], $cache);

        // The configuration that the cache has is the one that is used: here it
        // has another template for the component.
        $item = $cache->getItem($this->key([AlertComponent::class]));
        $this->assertTrue($item->isHit());
        $config = $item->get();
        $this->assertIsArray($config);
        $this->assertIsArray($config['test-alert']);
        $config['test-alert']['template'] = 'components/test-alert-alt.html.twig';
        $item->set($config);
        $cache->save($item);

        $html = $this->render('<twig:test-alert content="Hi" />', [AlertComponent::class], $cache);

        $this->assertSame('<aside class="alert-alt">Hi</aside>', trim($html));
    }

    public function testOtherComponentsDoNotUseTheConfigurationThatTheCacheHasForThese(): void
    {
        $cache = new ArrayAdapter();
        $this->render('<twig:test-alert content="Hi" />', [AlertComponent::class], $cache);

        // A deploy added a component: the cache of the other ones is not its own.
        $html = $this->render('<twig:test-box label="Label" class="wide" />', [AlertComponent::class, BoxComponent::class], $cache);

        $this->assertSame('<div class="wide">Label</div>', trim($html));
        $this->assertTrue($cache->getItem($this->key([AlertComponent::class]))->isHit());
        $this->assertTrue($cache->getItem($this->key([AlertComponent::class, BoxComponent::class]))->isHit());
        // The order of the components is not part of the key.
        $this->assertSame(
            $this->key([AlertComponent::class, BoxComponent::class]),
            $this->key([BoxComponent::class, AlertComponent::class])
        );
    }

    public function testWhatIsInTheCacheAndIsNotAConfigurationIsNotUsed(): void
    {
        $cache = new ArrayAdapter();

        $item = $cache->getItem($this->key([AlertComponent::class]));
        $item->set('garbage');
        $cache->save($item);

        // Without a valid configuration it is built again.
        $html = $this->render('<twig:test-alert content="Hi" />', [AlertComponent::class], $cache);
        $this->assertSame('<div class="alert alert-info">Hi</div>', trim($html));

        // Of a configuration, only the entries that have a name and are an array
        // are used.
        $config = $cache->getItem($this->key([AlertComponent::class]))->get();
        $this->assertIsArray($config);
        $item = $cache->getItem($this->key([AlertComponent::class]));
        $item->set(['not-an-entry' => 'text', 7 => ['no' => 'name'], ...$config]);
        $cache->save($item);

        $html = $this->render('<twig:test-alert content="Hi" />', [AlertComponent::class], $cache);
        $this->assertSame('<div class="alert alert-info">Hi</div>', trim($html));
    }

    public function testTheCacheCanBeOneOnDisk(): void
    {
        $directory = sys_get_temp_dir() . '/derafu-twig-registrar-' . bin2hex(random_bytes(4));

        try {
            $twig = $this->environment();
            (new ComponentRegistrar(new CacheItemPool('registrar', 3600, $directory)))->register($twig, [AlertComponent::class]);

            $this->assertSame(
                '<div class="alert alert-info">Hi</div>',
                trim($twig->createTemplate('<twig:test-alert content="Hi" />')->render())
            );
            $this->assertDirectoryExists($directory);
        } finally {
            $this->removeDirectory($directory);
        }
    }

    public function testWithoutCacheTheRegistrarUsesTheOneOfTheLibrary(): void
    {
        // Nothing is registered here: the default cache is on disk and is shared
        // by all that use it.
        $this->assertInstanceOf(ComponentRegistrar::class, new ComponentRegistrar());
    }

    private function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        foreach (new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        ) as $file) {
            $file instanceof SplFileInfo && ($file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname()));
        }
        rmdir($directory);
    }
}
