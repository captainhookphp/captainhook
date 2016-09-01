<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Hook\Message;

use sebastianfeldmann\CaptainHook\Config;
use sebastianfeldmann\CaptainHook\Console\IO\NullIO;
use sebastianfeldmann\CaptainHook\Git\CommitMessage;
use sebastianfeldmann\CaptainHook\Git\DummyRepo;
use sebastianfeldmann\CaptainHook\Git\Repository;

class RegexCheckTest extends \PHPUnit_Framework_TestCase
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
     * Tests RegexCheck::execute
     */
    public function testExecute()
    {
        $options = ['regex' => '#.*#'];
        $io      = new NullIO();
        $config  = new Config(HMU_PATH_FILES . '/captainhook.json');
        $action  = new Config\Action('php', '\\sebastianfeldmann\\CaptainHook\\Hook\\Message\\RegexCheck', $options);
        $repo    = new Repository($this->repo->getPath());
        $repo->setCommitMsg(new CommitMessage('Foo bar baz'));

        $standard = new RegexCheck();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests RegexCheck::execute
     *
     * @expectedException \Exception
     */
    public function testExecuteInvalidOption()
    {
        $io     = new NullIO();
        $config = new Config(HMU_PATH_FILES . '/captainhook.json');
        $repo   = new Repository($this->repo->getPath());
        $action = new Config\Action('php', '\\sebastianfeldmann\\CaptainHook\\Hook\\Message\\Rulebook');

        $standard = new RegexCheck();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests RegexCheck::execute
     *
     * @expectedException \Exception
     */
    public function testExecuteNoMatch()
    {
        $io     = new NullIO();
        $config = new Config(HMU_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(
            'php',
            '\\sebastianfeldmann\\CaptainHook\\Hook\\Message\\Rulebook',
            ['regex' => '#FooBarBaz#']
        );
        $repo   = new Repository($this->repo->getPath());
        $repo->setCommitMsg(new CommitMessage('Foo bar baz'));

        $standard = new RegexCheck();
        $standard->execute($config, $io, $repo, $action);
    }
}
