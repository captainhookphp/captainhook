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
     * Tests Util::pathToArray
     */
    public function testAbsolutePathToArray()
    {
        $path = Util::pathToArray('/foo/bar/baz');

        $this->assertCount(3, $path);
        $this->assertEquals('bar', $path[1]);
    }

    /**
     * Tests Util::pathToArray
     */
    public function testRelativePathToArray()
    {
        $path = Util::pathToArray('foo/bar/baz');

        $this->assertCount(3, $path);
        $this->assertEquals('bar', $path[1]);
    }

    /**
     * Tests Util::isSubDirectoryOf
     */
    public function testIsSubdirectory()
    {
        $this->assertTrue(Util::isSubDirectoryOf(Util::pathToArray('/foo/bar/baz'), Util::pathToArray('/foo/bar')));
        $this->assertTrue(Util::isSubDirectoryOf(Util::pathToArray('/foo/bar'), Util::pathToArray('/foo')));
        $this->assertFalse(Util::isSubDirectoryOf(Util::pathToArray('/foo/bar/baz'), Util::pathToArray('/fiz/baz')));
        $this->assertFalse(Util::isSubDirectoryOf(Util::pathToArray('/foo'), Util::pathToArray('/bar')));
    }

    /**
     * Tests Util::isSubDirectoryOf
     */
    public function testGetSubPathOfNoSubDirectory()
    {
        $this->expectException(\Exception::class);

        Util::getSubPathOf(Util::pathToArray('/foo/bar/baz'), Util::pathToArray('/fiz/baz'));
    }

    /**
     * Tests Template::getHookTargetPath
     */
    public function testGetTplTargetPath(): void
    {
        $path = Util::getTplTargetPath('/foo/bar', '/foo/bar/baz/vendor');
        $this->assertEquals('__DIR__ . \'/../../baz/vendor', $path);

        $path = Util::getTplTargetPath('/foo/bar', '/foo/bar/vendor');
        $this->assertEquals('__DIR__ . \'/../../vendor', $path);

        $path = Util::getTplTargetPath('/foo/bar', '/foo/bar/captainhook.json');
        $this->assertEquals('__DIR__ . \'/../../captainhook.json', $path);

        $path = Util::getTplTargetPath('/foo/bar', '/fiz/baz/captainhook.json');
        $this->assertEquals('\'/fiz/baz/captainhook.json', $path);
    }

    /**
     * Tests Util::resolveBinaryPath
     */
    public function testResolveBinaryPath(): void
    {
        $repoDir = realpath(__DIR__ . '/../../files/storage');
        $vendorDir = realpath(__DIR__ . '/../../files/storage');

        $path = Util::resolveBinaryPath($repoDir, $vendorDir, 'captainhook-run');
        $this->assertEquals($repoDir . '/captainhook-run', $path);

        $repoDir = __DIR__;
        $vendorDir = __DIR__;

        $path = Util::resolveBinaryPath($repoDir, $vendorDir, 'captainhook-run');
        $this->assertEquals(__DIR__ . '/bin/captainhook-run', $path);
    }
}
