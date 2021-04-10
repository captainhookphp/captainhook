<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner\Hook;

use CaptainHook\App\Config\Mockery as ConfigMockery;
use CaptainHook\App\Console\IO\Mockery as IOMockery;
use CaptainHook\App\Exception\ActionFailed;
use CaptainHook\App\Mockery as CHMockery;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SebastianFeldmann\Git\Operator\Index;
use SebastianFeldmann\Git\Operator\Status;
use SebastianFeldmann\Git\Repository;
use SebastianFeldmann\Git\Status\Path;

class PreCommitTest extends TestCase
{
    use ConfigMockery;
    use IOMockery;
    use CHMockery;

    /**
     * @var Repository&MockObject
     */
    private $repo;

    /**
     * @var Status&MockObject
     */
    private $statusOperator;

    protected function setUp(): void
    {
        $this->repo = $this->createRepositoryMock();

        $this->statusOperator = $this->getMockBuilder(Status::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->repo->method('getStatusOperator')->willReturn($this->statusOperator);
    }

    /**
     * Tests PreCommit::run
     *
     * @throws \Exception
     */
    public function testRunHookEnabled(): void
    {
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('not tested on windows');
        }

        // fail on first error must be active
        $config = $this->createConfigMock();
        $config->method('failOnFirstError')->willReturn(true);

        $io           = $this->createIOMock();
        $hookConfig   = $this->createHookConfigMock();
        $actionConfig = $this->createActionConfigMock();
        $actionConfig->method('getAction')->willReturn(CH_PATH_FILES . '/bin/success');
        $hookConfig->expects($this->once())->method('isEnabled')->willReturn(true);
        $hookConfig->expects($this->once())->method('getActions')->willReturn([$actionConfig]);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->atLeast(1))->method('write');

        $this->statusOperator->method('getWorkingTreeStatus')->willReturn([]);

        $runner = new PreCommit($io, $config, $this->repo);
        $runner->run();
    }

    /**
     * Tests PreCommit::run
     *
     * @throws \Exception
     */
    public function testRunHookDontFailOnFirstError(): void
    {
        $this->expectException(ActionFailed::class);

        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('not tested on windows');
        }
        // we have to create a config that does not fail on first error
        $config              = $this->createConfigMock();
        $config->expects($this->once())->method('failOnFirstError')->willReturn(false);

        $io                  = $this->createIOMock();
        $hookConfig          = $this->createHookConfigMock();
        $actionConfigFail    = $this->createActionConfigMock();
        $actionConfigSuccess = $this->createActionConfigMock();

        // every action has to get executed
        $actionConfigFail->expects($this->atLeastOnce())
                         ->method('getAction')
                         ->willReturn(CH_PATH_FILES . '/bin/failure');

        // so even if the first actions fails this action has to get executed
        $actionConfigSuccess->expects($this->atLeastOnce())
                            ->method('getAction')
                            ->willReturn(CH_PATH_FILES . '/bin/failure');

        $hookConfig->expects($this->once())->method('isEnabled')->willReturn(true);
        $hookConfig->expects($this->once())
                   ->method('getActions')
                   ->willReturn([$actionConfigFail, $actionConfigSuccess]);

        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->atLeast(1))->method('write');

        $this->statusOperator->method('getWorkingTreeStatus')->willReturn([]);

        $runner = new PreCommit($io, $config, $this->repo);
        $runner->run();
    }

    /**
     * Tests PreCommit::run
     *
     * @throws \Exception
     */
    public function testRunHookDisabled(): void
    {
        $io           = $this->createIOMock();
        $config       = $this->createConfigMock();
        $hookConfig   = $this->createHookConfigMock();
        $hookConfig->expects($this->once())->method('isEnabled')->willReturn(false);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->once())->method('write');

        $this->statusOperator->method('getWorkingTreeStatus')->willReturn([]);

        $runner = new PreCommit($io, $config, $this->repo);
        $runner->run();
    }

    public function testRunHookWithStatusPathsButZeroIntentToAddFiles(): void
    {
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('not tested on windows');
        }

        $config       = $this->createConfigMock();
        $io           = $this->createIOMock();
        $hookConfig   = $this->createHookConfigMock();
        $actionConfig = $this->createActionConfigMock();
        $actionConfig->method('getAction')->willReturn(CH_PATH_FILES . '/bin/success');
        $hookConfig->expects($this->once())->method('isEnabled')->willReturn(true);
        $hookConfig->expects($this->once())->method('getActions')->willReturn([$actionConfig]);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->atLeast(1))->method('write');

        $this->statusOperator->method('getWorkingTreeStatus')->willReturn([
            new Path('M ', 'foo/bar.php'),
            new Path('M ', 'foo/baz.php'),
        ]);

        $runner = new PreCommit($io, $config, $this->repo);
        $runner->run();
    }

    public function testRunHookWithIntentToAddFiles(): void
    {
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('not tested on windows');
        }

        $config       = $this->createConfigMock();
        $io           = $this->createIOMock();
        $hookConfig   = $this->createHookConfigMock();
        $actionConfig = $this->createActionConfigMock();
        $actionConfig->method('getAction')->willReturn(CH_PATH_FILES . '/bin/success');
        $hookConfig->expects($this->once())->method('isEnabled')->willReturn(true);
        $hookConfig->expects($this->once())->method('getActions')->willReturn([$actionConfig]);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->atLeast(1))->method('write');

        $statusPaths = [
            new Path('M ', 'foo/bar.php'),
            new Path(' A', 'foo/qux.php'),
            new Path('M ', 'foo/baz.php'),
            new Path(' A', 'foo/quux.php'),
        ];

        $indexOperator = $this->getMockBuilder(Index::class)
            ->disableOriginalConstructor()
            ->getMock();

        $indexOperator
            ->expects($this->once())
            ->method('removeFiles')
            ->with(['foo/qux.php', 'foo/quux.php'], false, true);

        $indexOperator
            ->expects($this->once())
            ->method('recordIntentToAddFiles')
            ->with(['foo/qux.php', 'foo/quux.php']);

        $this->statusOperator->method('getWorkingTreeStatus')->willReturn($statusPaths);
        $this->repo->method('getIndexOperator')->willReturn($indexOperator);

        $runner = new PreCommit($io, $config, $this->repo);
        $runner->run();
    }
}
