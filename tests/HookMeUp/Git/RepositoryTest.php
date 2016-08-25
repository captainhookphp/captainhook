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
     * Tests Repository::__construct
     *
     * @expectedException \Exception
     */
    public function testInvalidRepository()
    {
        $repository = new Repository(realpath(__DIR__ . '/../../files/git/repo-invalid'));
        $this->assertFalse(is_a($repository, '\\HookMeUp\\Git\\Repository'));
    }

    /**
     * Tests Repository::getHooks
     */
    public function testGetHooksDir()
    {
        $repoPath   = realpath(__DIR__ . '/../../files/git/repo-default');
        $repository = new Repository($repoPath);
        $this->assertEquals($repoPath . '/.git/hooks', $repository->getHooksDir());
    }

    /**
     * Tests Repository::hookExists
     */
    public function testHookExists()
    {
        $repoPath   = realpath(__DIR__ . '/../../files/git/repo-default');
        $repository = new Repository($repoPath);

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
        $repoPath   = realpath(__DIR__ . '/../../files/git/repo-default');
        $repository = new Repository($repoPath);
        $repository->getCommitMsg();
    }
}
