<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\Composer\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO\NullIO;
use CaptainHook\App\Mockery;
use Exception;
use PHPUnit\Framework\TestCase;

class CheckLockFileTest extends TestCase
{
    use Mockery;

    /**
     * Tests CheckLockFile::execute
     *
     * @throws \Exception
     */
    public function testExecute(): void
    {
        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo   = $this->createRepositoryMock();
        $action = new Config\Action(
            CheckLockFile::class,
            ['path' => CH_PATH_FILES . '/composer/valid', 'name' => 'composer.fake']
        );
        $standard = new CheckLockFile();
        $standard->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
    }

    /**
     * Tests CheckLockFile::execute
     *
     * @throws \Exception
     */
    public function testExecuteFail(): void
    {
        $this->expectException(Exception::class);

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo   = $this->createRepositoryMock();
        $action = new Config\Action(
            CheckLockFile::class,
            ['path' => CH_PATH_FILES . '/composer/invalid-hash', 'name' => 'composer.fake']
        );

        $standard = new CheckLockFile();
        $standard->execute($config, $io, $repo, $action);
    }


    /**
     * Tests CheckLockFile::execute
     *
     * @throws \Exception
     */
    public function testExecuteNoHash(): void
    {
        $this->expectException(Exception::class);

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo   = $this->createRepositoryMock();
        $action = new Config\Action(
            CheckLockFile::class,
            ['path' => CH_PATH_FILES . '/composer/no-hash', 'name' => 'composer.fake']
        );

        $standard = new CheckLockFile();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests CheckLockFile::execute
     *
     * @throws \Exception
     */
    public function testExecuteInvalidPath(): void
    {
        $this->expectException(Exception::class);

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo   = $this->createRepositoryMock();
        $action = new Config\Action(
            CheckLockFile::class,
            ['path' => CH_PATH_FILES . '/composer/not-there']
        );
        $standard = new CheckLockFile();
        $standard->execute($config, $io, $repo, $action);
    }
}
