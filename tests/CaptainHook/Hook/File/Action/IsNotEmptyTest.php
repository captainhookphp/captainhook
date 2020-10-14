<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\File\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO\NullIO;
use CaptainHook\App\Mockery as GitMockery;
use CaptainHook\App\Console\IO\Mockery as IOMockery;
use PHPUnit\Framework\TestCase;

class IsNotEmptyTest extends TestCase
{
    use GitMockery;
    use IOMockery;

    /**
     * Tests IsNotEmpty::execute
     *
     * @throws \Exception
     */
    public function testFile(): void
    {
        $io     = new NullIO();
        $repo   = $this->createRepositoryMock();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(
            IsNotEmpty::class,
            [
                'files' => [
                    CH_PATH_FILES . '/storage/test.json'
                ]
            ]
        );

        $isNotEmpty = new IsNotEmpty();
        $isNotEmpty->execute($config, $io, $repo, $action);

        // no error should happen
        $this->assertTrue(true);
    }

    /**
     * Tests IsNotEmpty::execute
     *
     * @throws \Exception
     */
    public function testDirectory(): void
    {
        $io     = new NullIO();
        $repo   = $this->createRepositoryMock();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(
            IsNotEmpty::class,
            [
                'files' => [
                    CH_PATH_FILES . '/storage/',
                ]
            ]
        );

        $isNotEmpty = new IsNotEmpty();
        $isNotEmpty->execute($config, $io, $repo, $action);

        // no error should happen
        $this->assertTrue(true);
    }

    /**
     * Tests IsNotEmpty::execute
     *
     * @throws \Exception
     */
    public function testEmptyDirectoryFail(): void
    {
        // create empty directory
        $emptyDir = sys_get_temp_dir() . '/emptyDir-' . random_int(100, 999);
        mkdir($emptyDir, 0777);

        $io     = new NullIO();
        $repo   = $this->createRepositoryMock();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(
            IsNotEmpty::class,
            [
                'files' => [
                    $emptyDir,
                ]
            ]
        );

        try {
            $isNotEmpty = new IsNotEmpty();
            $isNotEmpty->execute($config, $io, $repo, $action);
        } catch (\Exception $e) {
            rmdir($emptyDir);
            $this->assertTrue(true);
        }
    }

    /**
     * Tests IsNotEmpty::execute
     *
     * @throws \Exception
     */
    public function testGlob(): void
    {
        $io     = $this->createIOMock();
        $repo   = $this->createRepositoryMock();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(
            IsNotEmpty::class,
            [
                'files' => [
                    CH_PATH_FILES . '/storage/*.txt',
                    CH_PATH_FILES . '/storage/*.json',
                ]
            ]
        );

        // with this configuration the Cap'n should find 4 files for 2 patterns
        $io->expects($this->exactly(3))->method('write');

        $isNotEmpty = new IsNotEmpty();
        $isNotEmpty->execute($config, $io, $repo, $action);

        // no error should happen
        $this->assertTrue(true);
    }

    /**
     * Tests IsNotEmpty::execute
     *
     * @throws \Exception
     */
    public function testFileDoesNotExistFail(): void
    {
        $this->expectException(\Exception::class);

        $io     = new NullIO();
        $repo   = $this->createRepositoryMock();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(
            IsNotEmpty::class,
            [
                'files' => [
                    CH_PATH_FILES . '/doesNotExist.txt',
                ]
            ]
        );

        $isNotEmpty = new IsNotEmpty();
        $isNotEmpty->execute($config, $io, $repo, $action);
    }

    /**
     * Tests IsNotEmpty::execute
     *
     * @throws \Exception
     */
    public function testFileEmptyFail(): void
    {
        $this->expectException(\Exception::class);

        $io     = new NullIO();
        $repo   = $this->createRepositoryMock();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(
            IsNotEmpty::class,
            [
                'files' => [
                    CH_PATH_FILES . '/storage/empty.log',
                ]
            ]
        );

        $isNotEmpty = new IsNotEmpty();
        $isNotEmpty->execute($config, $io, $repo, $action);
    }
}
