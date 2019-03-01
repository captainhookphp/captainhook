<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook\Composer\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO\NullIO;
use CaptainHook\App\Git\CommitMessage;
use CaptainHook\App\Git\DummyRepo;
use SebastianFeldmann\Git\Repository;
use PHPUnit\Framework\TestCase;

class CheckLockFileTest extends TestCase
{
    /**
     * @var \CaptainHook\App\Git\DummyRepo
     */
    private $repo;

    /**
     * Setup dummy repo.
     */
    public function setUp(): void
    {
        $this->repo = new DummyRepo();
        $this->repo->setup();
    }

    /**
     * Cleanup dummy repo.
     */
    public function tearDown(): void
    {
        $this->repo->cleanup();
    }

    /**
     * Tests CheckLockFile::execute
     */
    public function testExecute()
    {
        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo   = new Repository($this->repo->getPath());
        $action = new Config\Action(
            'php',
            CheckLockFile::class,
            ['path' => CH_PATH_FILES . '/composer/valid']
        );
        $standard = new CheckLockFile();
        $standard->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
    }

    /**
     * Tests CheckLockFile::execute
     */
    public function testExecuteFail()
    {
        $this->expectException(\Exception::class);

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo   = new Repository($this->repo->getPath());
        $action = new Config\Action(
            'php',
            CheckLockFile::class,
            ['path' => CH_PATH_FILES . '/composer/invalid-hash']
        );

        $standard = new CheckLockFile();
        $standard->execute($config, $io, $repo, $action);
    }


    /**
     * Tests CheckLockFile::execute
     */
    public function testExecuteNoHash()
    {
        $this->expectException(\Exception::class);

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo   = new Repository($this->repo->getPath());
        $action = new Config\Action(
            'php',
            CheckLockFile::class,
            ['path' => CH_PATH_FILES . '/composer/no-hash']
        );

        $standard = new CheckLockFile();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests CheckLockFile::execute
     */
    public function testExecuteInvalidPath()
    {
        $this->expectException(\Exception::class);

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo   = new Repository($this->repo->getPath());
        $action = new Config\Action(
            'php',
            CheckLockFile::class,
            ['path' => CH_PATH_FILES . '/composer/not-there']
        );
        $standard = new CheckLockFile();
        $standard->execute($config, $io, $repo, $action);
    }
}
