<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Git;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \HookMeUp\Git\DummyRepo
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
     * Tests Repository::__construct
     *
     * @expectedException \Exception
     */
    public function testInvalidRepository()
    {
        $repository = new Repository('invalidGitRepo');
        $this->assertFalse(is_a($repository, '\\HookMeUp\\Git\\Repository'));
    }

    /**
     * Tests Repository::getHooks
     */
    public function testGetHooksDir()
    {
        $repository = new Repository($this->repo->getPath());
        $this->assertEquals($this->repo->getPath() . '/.git/hooks', $repository->getHooksDir());
    }

    /**
     * Tests Repository::hookExists
     */
    public function testHookExists()
    {
        $this->repo->touchHook('pre-commit');

        $repository = new Repository($this->repo->getPath());

        $this->assertTrue($repository->hookExists('pre-commit'));
        $this->assertFalse($repository->hookExists('pre-push'));
    }

    /**
     * Tests Repository::getCommitMsg
     *
     * @expectedException \Exception
     */
    public function testGetCommitMessageFail()
    {
        $repository = new Repository($this->repo->getPath());
        $repository->getCommitMsg();
    }
}
