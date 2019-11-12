<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Storage;

use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase
{
    /**
     * Tests Util::arrayToPath
     */
    public function testAbsoluteArrayToPath(): void
    {
        $expected = DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar';
        $this->assertEquals($expected, Util::arrayToPath(['foo', 'bar'], true));
    }

    /**
     * Tests Util::arrayToPath
     */
    public function testRelativeArrayToPath(): void
    {
        $expected = 'foo' . DIRECTORY_SEPARATOR . 'bar';
        $this->assertEquals($expected, Util::arrayToPath(['foo', 'bar']));
    }

    /**
     * Tests Util::pathToArray
     */
    public function testAbsolutePathToArray(): void
    {
        $absolute = DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar' . DIRECTORY_SEPARATOR . 'baz';
        $path     = Util::pathToArray($absolute);

        $this->assertCount(3, $path);
        $this->assertEquals('bar', $path[1]);
    }

    /**
     * Tests Util::pathToArray
     */
    public function testRelativePathToArray(): void
    {
        $relative = 'foo' . DIRECTORY_SEPARATOR . 'bar' . DIRECTORY_SEPARATOR . 'baz';
        $path     = Util::pathToArray($relative);

        $this->assertCount(3, $path);
        $this->assertEquals('bar', $path[1]);
    }

    /**
     * Tests Util::isSubDirectoryOf
     */
    public function testIsSubdirectory(): void
    {
        $this->assertTrue(Util::isSubDirectoryOf(['foo', 'bar', 'baz'], ['foo', 'bar']));
        $this->assertTrue(Util::isSubDirectoryOf(['foo', 'bar'], ['foo']));
        $this->assertFalse(Util::isSubDirectoryOf(['foo', 'bar', 'baz'], ['fiz', 'baz']));
        $this->assertFalse(Util::isSubDirectoryOf(['foo'], ['bar']));
    }

    /**
     * Tests Util::isSubDirectoryOf
     */
    public function testGetSubPathOf(): void
    {
        $this->assertEquals(['baz'], Util::getSubPathOf(['foo', 'bar', 'baz'],['foo', 'bar']));
        $this->assertEquals(['baz', 'buz'], Util::getSubPathOf(['foo', 'bar', 'baz', 'buz'], ['foo', 'bar']));
    }

    /**
     * Tests Util::isSubDirectoryOf
     */
    public function testGetSubPathOfNoSubDirectory(): void
    {
        $this->expectException(\Exception::class);

        Util::getSubPathOf(['foo', 'bar', 'baz'], ['fiz', 'baz']);
    }

    /**
     * Tests Template::getHookTargetPath
     */
    public function testGetTplTargetPath(): void
    {
        $repo   = Util::arrayToPath(['foo', 'bar'], true);
        $target = Util::arrayToPath(['foo', 'bar', 'baz', 'vendor'], true);
        $this->assertEquals('__DIR__ . \'/../../baz/vendor', Util::getTplTargetPath($repo, $target));

        $repo   = Util::arrayToPath(['foo', 'bar'], true);
        $target = Util::arrayToPath(['foo', 'bar', 'vendor'], true);
        $this->assertEquals('__DIR__ . \'/../../vendor', Util::getTplTargetPath($repo, $target));

        $repo   = Util::arrayToPath(['foo', 'bar'], true);
        $target = Util::arrayToPath(['foo', 'bar', 'captainhook.json'], true);
        $this->assertEquals('__DIR__ . \'/../../captainhook.json', Util::getTplTargetPath($repo, $target));

        $repo   = Util::arrayToPath(['foo', 'bar'], true);
        $target = Util::arrayToPath(['fiz', 'baz', 'captainhook.json'], true);
        $this->assertEquals('\'/fiz/baz/captainhook.json', Util::getTplTargetPath($repo, $target));
    }

    /**
     * Tests Util::resolveBinaryPath
     */
    public function testResolveBinaryPath(): void
    {
        $repoDir = realpath(__DIR__ . '/../../files/storage');
        $vendorDir = realpath(__DIR__ . '/../../files/storage');

        $path = Util::resolveBinaryPath($repoDir, $vendorDir, 'captainhook-run');
        $this->assertEquals($repoDir . DIRECTORY_SEPARATOR . 'captainhook-run', $path);

        $repoDir   = __DIR__;
        $vendorDir = __DIR__;

        $path = Util::resolveBinaryPath($repoDir, $vendorDir, 'captainhook-run');
        $this->assertEquals(__DIR__ . '/bin/captainhook-run', $path);
    }
}
