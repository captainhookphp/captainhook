<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\Message\Action;

use CaptainHook\App\Config\Mockery as ConfigMockery;
use CaptainHook\App\Config\Options;
use CaptainHook\App\Console\IO\Mockery as IOMockery;
use CaptainHook\App\Exception\ActionFailed;
use CaptainHook\App\Mockery as CHMockery;
use CaptainHook\App\RepoMock;
use SebastianFeldmann\Git\CommitMessage;
use PHPUnit\Framework\TestCase;

class InjectIssueKeyFromBranchTest extends TestCase
{
    use ConfigMockery;
    use CHMockery;
    use IOMockery;

    /**
     * Tests InjectIssueKeyFromBranch::execute
     *
     * @throws \Exception
     */
    public function testPrependSubject(): void
    {
        $repo = new RepoMock();
        $info = $this->createGitInfoOperator('5.0.0', 'freature/ABCD-12345-foo-bar-baz');

        $repo->setCommitMsg(new CommitMessage('foo' . PHP_EOL . PHP_EOL . 'bar'));
        $repo->setInfoOperator($info);

        $io      = $this->createIOMock();
        $config  = $this->createConfigMock();
        $action  = $this->createActionConfigMock();
        $action->method('getOptions')->willReturn(new Options([
            'into' => 'subject',
            'mode' => 'prepend',
        ]));

        $hook = new InjectIssueKeyFromBranch();
        $hook->execute($config, $io, $repo, $action);

        $this->assertEquals('ABCD-12345 foo', $repo->getCommitMsg()->getSubject());
    }

    /**
     * Tests InjectIssueKeyFromBranch::execute
     *
     * @throws \Exception
     */
    public function testAppendSubject(): void
    {
        $repo = new RepoMock();
        $info = $this->createGitInfoOperator('5.0.0', 'freature/ABCD-12345-foo-bar-baz');

        $repo->setCommitMsg(new CommitMessage('foo' . PHP_EOL . PHP_EOL . 'bar'));
        $repo->setInfoOperator($info);

        $io      = $this->createIOMock();
        $config  = $this->createConfigMock();
        $action  = $this->createActionConfigMock();
        $action->method('getOptions')->willReturn(new Options([
            'into' => 'subject',
            'mode' => 'append',
        ]));

        $hook = new InjectIssueKeyFromBranch();
        $hook->execute($config, $io, $repo, $action);

        $this->assertEquals('foo ABCD-12345', $repo->getCommitMsg()->getSubject());
    }

    /**
     * Tests InjectIssueKeyFromBranch::execute
     *
     * @throws \Exception
     */
    public function testAppendBodyWithPrefix(): void
    {
        $repo = new RepoMock();
        $info = $this->createGitInfoOperator('5.0.0', 'freature/ABCD-12345-foo-bar-baz');

        $repo->setCommitMsg(new CommitMessage('foo' . PHP_EOL . PHP_EOL . 'bar'));
        $repo->setInfoOperator($info);

        $io      = $this->createIOMock();
        $config  = $this->createConfigMock();
        $action  = $this->createActionConfigMock();
        $action->method('getOptions')->willReturn(new Options([
            'into'   => 'body',
            'mode'   => 'append',
            'prefix' => PHP_EOL . PHP_EOL . 'issue: '
        ]));

        $hook = new InjectIssueKeyFromBranch();
        $hook->execute($config, $io, $repo, $action);

        $this->assertEquals('bar' . PHP_EOL . PHP_EOL . 'issue: ABCD-12345', $repo->getCommitMsg()->getBody());
    }

    /**
     * Tests InjectIssueKeyFromBranch::execute
     *
     * @throws \Exception
     */
    public function testIgnoreIssueKeyNotFound(): void
    {
        $repo = new RepoMock();
        $info = $this->createGitInfoOperator('5.0.0', 'foo');

        $repo->setCommitMsg(new CommitMessage('foo' . PHP_EOL . PHP_EOL . 'bar'));
        $repo->setInfoOperator($info);

        $io      = $this->createIOMock();
        $config  = $this->createConfigMock();
        $action  = $this->createActionConfigMock();
        $action->method('getOptions')->willReturn(new Options([
            'into'   => 'body',
            'mode'   => 'append',
            'prefix' => PHP_EOL . PHP_EOL . 'issue: '
        ]));

        $hook = new InjectIssueKeyFromBranch();
        $hook->execute($config, $io, $repo, $action);

        $this->assertEquals('bar', $repo->getCommitMsg()->getBody());
    }

    /**
     * Tests InjectIssueKeyFromBranch::execute
     *
     * @throws \Exception
     */
    public function testFailIssueKeyNotFound(): void
    {
        $this->expectException(ActionFailed::class);

        $repo = new RepoMock();
        $info = $this->createGitInfoOperator('5.0.0', 'foo');

        $repo->setCommitMsg(new CommitMessage('foo' . PHP_EOL . PHP_EOL . 'bar'));
        $repo->setInfoOperator($info);

        $io      = $this->createIOMock();
        $config  = $this->createConfigMock();
        $action  = $this->createActionConfigMock();
        $action->method('getOptions')->willReturn(new Options([
            'force' => true,
        ]));

        $hook = new InjectIssueKeyFromBranch();
        $hook->execute($config, $io, $repo, $action);
    }

    /**
     * Tests InjectIssueKeyFromBranch::execute
     *
     * @throws \Exception
     */
    public function testIssueKeyAlreadyInMSG(): void
    {
        $repo = new RepoMock();
        $info = $this->createGitInfoOperator('5.0.0', 'freature/ABCD-12345-foo-bar-baz');

        $repo->setCommitMsg(new CommitMessage('ABCD-12345 foo' . PHP_EOL . PHP_EOL . 'bar'));
        $repo->setInfoOperator($info);

        $io      = $this->createIOMock();
        $config  = $this->createConfigMock();
        $action  = $this->createActionConfigMock();
        $action->method('getOptions')->willReturn(new Options([
            'into' => 'subject',
        ]));

        $hook = new InjectIssueKeyFromBranch();
        $hook->execute($config, $io, $repo, $action);

        $this->assertEquals('ABCD-12345 foo', $repo->getCommitMsg()->getSubject());
    }
}
