<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Hook\Composer\Action;

use SebastianFeldmann\CaptainHook\Config;
use SebastianFeldmann\CaptainHook\Console\IO\NullIO;
use SebastianFeldmann\CaptainHook\Git\CommitMessage;
use SebastianFeldmann\CaptainHook\Git\DummyRepo;
use SebastianFeldmann\Git\Repository;

class CheckLockFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \SebastianFeldmann\CaptainHook\Git\DummyRepo
     */
    private $repo;

    /**
     * Setup dummy repo.
     */
    public function setUp()
    {
        $this->repo = new DummyRepo();
        $this->repo->setup();
    }

    /**
     * Cleanup dummy repo.
     */
    public function tearDown()
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
            '\\SebastianFeldmann\\CaptainHook\\Hook\\Composer\\Action\\CheckLockFile',
            ['path' => CH_PATH_FILES . '/composer/valid']
        );
        $standard = new CheckLockFile();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests CheckLockFile::execute
     *
     * @expectedException \Exception
     */
    public function testExecuteFail()
    {
        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo   = new Repository($this->repo->getPath());
        $action = new Config\Action(
            'php',
            '\\SebastianFeldmann\\CaptainHook\\Hook\\Composer\\Action\\CheckLockFile',
            ['path' => CH_PATH_FILES . '/composer/invalid']
        );

        $standard = new CheckLockFile();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests CheckLockFile::execute
     *
     * @expectedException \Exception
     */
    public function testExecuteInvalidPath()
    {
        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo   = new Repository($this->repo->getPath());
        $action = new Config\Action(
            'php',
            '\\SebastianFeldmann\\CaptainHook\\Hook\\Composer\\Action\\CheckLockFile',
            ['path' => CH_PATH_FILES . '/composer/not-there']
        );
        $standard = new CheckLockFile();
        $standard->execute($config, $io, $repo, $action);
    }
}
