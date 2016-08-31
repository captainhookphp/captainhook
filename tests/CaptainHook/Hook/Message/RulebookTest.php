<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook\Message;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO\NullIO;
use CaptainHook\App\Git\CommitMessage;
use CaptainHook\App\Git\DummyRepo;
use CaptainHook\App\Git\Repository;

class RulebookTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \CaptainHook\App\Git\DummyRepo
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
     * Tests Rulebook::execute
     */
    public function testExecute()
    {
        $io     = new NullIO();
        $config = new Config(HMU_PATH_FILES . '/captainhook.json');
        $action = new Config\Action('php', '\\CaptainHook\\App\\Hook\\Message\\Beams');
        $repo   = new Repository($this->repo->getPath());
        $repo->setCommitMsg(new CommitMessage('Foo bar baz'));

        $standard = new Beams();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests Rulebook::execute
     *
     * @expectedException \Exception
     */
    public function testExecuteClassNotFound()
    {
        $io     = new NullIO();
        $config = new Config(HMU_PATH_FILES . '/captainhook.json');
        $action = new Config\Action('php', '\\CaptainHook\\App\\Hook\\Message\\Rulebook', ['\\CaptainHook\\App\\Foo']);
        $repo   = new Repository($this->repo->getPath());

        $standard = new Rulebook();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests Rulebook::execute
     *
     * @expectedException \Exception
     */
    public function testExecuteInvalidClass()
    {
        $io     = new NullIO();
        $config = new Config(HMU_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(
            'php',
            '\\CaptainHook\\App\\Hook\\Message\\Rulebook',
            ['\\CaptainHook\\App\\Hook\\Message\\Validator']
        );
        $repo   = new Repository($this->repo->getPath());

        $standard = new Rulebook();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests Rulebook::execute
     */
    public function testExecuteValidRule()
    {
        $io     = new NullIO();
        $config = new Config(HMU_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(
            'php',
            '\\CaptainHook\\App\\Hook\\Message\\Rulebook',
            ['\\CaptainHook\\App\\Hook\\Message\\Validator\\Rule\\CapitalizeSubject']
        );
        $repo   = new Repository($this->repo->getPath());
        $repo->setCommitMsg(new CommitMessage('Foo bar baz'));

        $standard = new Rulebook();
        $standard->execute($config, $io, $repo, $action);
    }
}
