<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Hook\Message\Check;

use sebastianfeldmann\CaptainHook\Config;
use sebastianfeldmann\CaptainHook\Console\IO\NullIO;
use sebastianfeldmann\CaptainHook\Git\CommitMessage;
use sebastianfeldmann\CaptainHook\Git\DummyRepo;
use sebastianfeldmann\CaptainHook\Git\Repository;

class BeamsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \sebastianfeldmann\CaptainHook\Git\DummyRepo
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
     * Tests Beams::execute
     */
    public function testExecute()
    {
        $io     = new NullIO();
        $config = new Config(HMU_PATH_FILES . '/captainhook.json');
        $action = new Config\Action('php', '\\sebastianfeldmann\\CaptainHook\\Hook\\Message\\Beams');
        $repo   = new Repository($this->repo->getPath());
        $repo->setCommitMsg(new CommitMessage('Foo bar baz'));

        $standard = new Beams();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests Beams::execute
     *
     * @expectedException \Exception
     */
    public function testExecuteFail()
    {
        $io     = new NullIO();
        $config = new Config(HMU_PATH_FILES . '/captainhook.json');
        $action = new Config\Action('php', '\\sebastianfeldmann\\CaptainHook\\Hook\\Message\\Beams');
        $repo   = new Repository($this->repo->getPath());
        $repo->setCommitMsg(new CommitMessage('foo bar baz.'));

        $standard = new Beams();
        $standard->execute($config, $io, $repo, $action);
    }
}
