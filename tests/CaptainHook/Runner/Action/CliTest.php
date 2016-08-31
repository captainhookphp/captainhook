<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\Runner\Action;

use CaptainHook\Runner\BaseTestRunner;

class CliTest extends BaseTestRunner
{
    /**
     * Tests Cli::execute
     */
    public function testExecuteSuccess()
    {
        $config = $this->getConfigMock();
        $io     = $this->getIOMock();
        $repo   = $this->getRepositoryMock();
        $action = $this->getActionConfigMock();

        $cmd = HMU_PATH_FILES . '/bin/success';

        $io->expects($this->once())->method('write');
        $action->expects($this->once())->method('getAction')->willReturn($cmd);

        $cli = new Cli();
        $cli->execute($config, $io, $repo, $action);
    }

    /**
     * Tests Cli::execute
     *
     * @expectedException \Exception
     */
    public function testExecuteFailure()
    {
        $config = $this->getConfigMock();
        $io     = $this->getIOMock();
        $repo   = $this->getRepositoryMock();
        $action = $this->getActionConfigMock();

        $cmd = HMU_PATH_FILES . '/bin/failure';

        $action->expects($this->once())->method('getAction')->willReturn($cmd);

        $cli = new Cli();
        $cli->execute($config, $io, $repo, $action);
    }
}
