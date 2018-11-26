<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook\Message\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO\NullIO;
use CaptainHook\App\Git\DummyRepo;
use SebastianFeldmann\Git\CommitMessage;
use SebastianFeldmann\Git\Repository;

class RulesTest extends \PHPUnit\Framework\TestCase
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
    public function testExecuteEmptyRules()
    {
        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action('php', '\\CaptainHook\\App\\Hook\\Message\\Action\\Rules');
        $repo   = new Repository($this->repo->getPath());
        $repo->setCommitMsg(new CommitMessage('Foo bar baz'));

        $standard = new Rules();
        $standard->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
    }

    /**
     * Tests Rulebook::execute
     *
     * @expectedException \Exception
     */
    public function testExecuteClassNotFound()
    {
        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo   = new Repository($this->repo->getPath());
        $action = new Config\Action(
            'php',
            '\\CaptainHook\\App\\Hook\\Message\\Action\\Rules',
            ['\\CaptainHook\\App\\Foo']
        );

        $standard = new Rules();
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
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(
            'php',
            '\\CaptainHook\\App\\Hook\\Message\\Action\\Rules',
            ['\\CaptainHook\\App\\Hook\\Message\\Validator']
        );
        $repo   = new Repository($this->repo->getPath());

        $standard = new Rules();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests Rulebook::execute
     */
    public function testExecuteValidRule()
    {
        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(
            'php',
            '\\CaptainHook\\App\\Hook\\Message\\Action\\Rules',
            ['\\CaptainHook\\App\\Hook\\Message\\Rule\\CapitalizeSubject']
        );
        $repo   = new Repository($this->repo->getPath());
        $repo->setCommitMsg(new CommitMessage('Foo bar baz'));

        $standard = new Rules();
        $standard->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
    }

    /**
     * Tests Rule::execute
     *
     * @expectedException \Exception
     */
    public function testNoRule()
    {
        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(
            'php',
            '\\CaptainHook\\App\\Hook\\Message\\Action\\Rules',
            ['\\CaptainHook\\App\\Hook\\Message\\Action\\NoRule']
        );
        $repo   = new Repository($this->repo->getPath());
        $repo->setCommitMsg(new CommitMessage('Foo bar baz'));

        $standard = new Rules();
        $standard->execute($config, $io, $repo, $action);
    }
}

class NoRule
{
}
