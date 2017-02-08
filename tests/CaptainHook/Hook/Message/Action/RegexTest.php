<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Hook\Message\Action;

use SebastianFeldmann\CaptainHook\Config;
use SebastianFeldmann\CaptainHook\Console\IO\NullIO;
use SebastianFeldmann\CaptainHook\Git\DummyRepo;
use SebastianFeldmann\Git\CommitMessage;
use SebastianFeldmann\Git\Repository;

class RegexTest extends \PHPUnit\Framework\TestCase
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
     * Tests RegexCheck::execute
     */
    public function testExecute()
    {
        $options = ['regex' => '#.*#'];
        $io      = new NullIO();
        $config  = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo    = new Repository($this->repo->getPath());
        $action  = new Config\Action(
            'php',
            '\\SebastianFeldmann\\CaptainHook\\Hook\\Message\\Action\\RegexCheck',
            $options
        );
        $repo->setCommitMsg(new CommitMessage('Foo bar baz'));

        $standard = new Regex();
        $standard->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
    }

    /**
     * Tests RegexCheck::execute
     *
     * @expectedException \Exception
     */
    public function testExecuteInvalidOption()
    {
        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo   = new Repository($this->repo->getPath());
        $action = new Config\Action('php', '\\SebastianFeldmann\\CaptainHook\\Hook\\Message\\Action\\Rulebook');

        $standard = new Regex();
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
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(
            'php',
            '\\SebastianFeldmann\\CaptainHook\\Hook\\Message\\Rulebook',
            ['regex' => '#FooBarBaz#']
        );
        $repo   = new Repository($this->repo->getPath());
        $repo->setCommitMsg(new CommitMessage('Foo bar baz'));

        $standard = new Regex();
        $standard->execute($config, $io, $repo, $action);
    }
}
