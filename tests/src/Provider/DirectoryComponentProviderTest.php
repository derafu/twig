<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsTwig\Provider;

use Derafu\TestsTwig\Fixture\Component\AlertComponent;
use Derafu\TestsTwig\Fixture\Component\BoxComponent;
use Derafu\TestsTwig\Fixture\Component\GreetingComponent;
use Derafu\TestsTwig\Fixture\Component\TagComponent;
use Derafu\TestsTwig\Fixture\Component\UnnamedComponent;
use Derafu\Translation\Contract\TranslatableInterface;
use Derafu\Twig\Provider\DirectoryComponentProvider;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Throwable;

/**
 * A directory of components that does not exist, or a file of it that can not be
 * read, is a translatable error.
 */
#[CoversClass(DirectoryComponentProvider::class)]
final class DirectoryComponentProviderTest extends TestCase
{
    public function testADirectoryThatDoesNotExistIsATranslatableError(): void
    {
        $exception = null;
        try {
            new DirectoryComponentProvider('/does/not/exist');
        } catch (Throwable $e) {
            $exception = $e;
        }

        $this->assertInstanceOf(InvalidArgumentException::class, $exception);
        $this->assertInstanceOf(TranslatableInterface::class, $exception);
        $this->assertSame('Directory "/does/not/exist" does not exist.', $exception->getMessage());
    }

    public function testAFileThatCanNotBeReadIsATranslatableError(): void
    {
        if (function_exists('posix_geteuid') && posix_geteuid() === 0) {
            $this->markTestSkipped('The root user reads any file.');
        }

        $directory = sys_get_temp_dir() . '/derafu-twig-components-' . bin2hex(random_bytes(4));
        mkdir($directory);
        $file = $directory . '/Hidden.php';
        file_put_contents($file, "<?php\nclass Hidden {}\n");
        chmod($file, 0000);

        try {
            $provider = new DirectoryComponentProvider($directory, new ArrayAdapter());

            $exception = null;
            try {
                $provider->getComponents();
            } catch (Throwable $e) {
                $exception = $e;
            }
        } finally {
            chmod($file, 0644);
            unlink($file);
            rmdir($directory);
        }

        $this->assertInstanceOf(RuntimeException::class, $exception);
        $this->assertInstanceOf(TranslatableInterface::class, $exception);
        $this->assertStringStartsWith('The file ', $exception->getMessage());
        $this->assertStringEndsWith('/Hidden.php can not be read.', $exception->getMessage());
    }

    public function testTheComponentsOfTheDirectoryAreTheClassesWithTheAttribute(): void
    {
        $directory = __DIR__ . '/../Fixture/Component';
        $cache = new ArrayAdapter();

        $first = (new DirectoryComponentProvider($directory, $cache))->getComponents();
        // Another provider over the same cache reads them from it.
        $second = (new DirectoryComponentProvider($directory, $cache))->getComponents();

        $expected = [AlertComponent::class, BoxComponent::class, GreetingComponent::class, TagComponent::class, UnnamedComponent::class];
        $this->assertEqualsCanonicalizing($expected, $first);
        $this->assertEqualsCanonicalizing($expected, $second);
    }

    public function testWhatIsNotAClassThatCanBeLoadedIsNotAComponent(): void
    {
        $directory = sys_get_temp_dir() . '/derafu-twig-provider-' . bin2hex(random_bytes(4));
        mkdir($directory);
        // A file without a class, and one with a class that nothing loads.
        file_put_contents($directory . '/functions.php', "<?php\n\nreturn 1;\n");
        file_put_contents($directory . '/Ghost.php', "<?php\n\nnamespace Derafu\\TestsTwig\\Ghost;\n\nclass Ghost\n{\n}\n");

        try {
            $components = (new DirectoryComponentProvider($directory, new ArrayAdapter()))->getComponents();
        } finally {
            unlink($directory . '/functions.php');
            unlink($directory . '/Ghost.php');
            rmdir($directory);
        }

        $this->assertSame([], $components);
    }
}
