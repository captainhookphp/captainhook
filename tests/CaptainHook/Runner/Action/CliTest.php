<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Runner\Action;

use CaptainHook\App\Config\Options;
use CaptainHook\App\Runner\BaseTestRunner;

class CliTest extends BaseTestRunner
{
    /**
     * Tests Cli::execute
     */
    public function testExecuteSuccess()
    {
        $args   = [];
        $io     = $this->getIOMock();
        $action = $this->getActionConfigMock();
        $cmd    = CH_PATH_FILES . '/bin/success';

        $io->expects($this->once())->method('getArguments')->willReturn($args);
        $io->expects($this->once())->method('write');
        $action->expects($this->once())->method('getAction')->willReturn($cmd);

        $cli = new Cli();
        $cli->execute($io, $action);
    }

    /**
     * Tests Cli::execute
     */
    public function testExecuteSuccessWithReplacements()
    {
        $args   = ['file' => 'bin', 'mode' => 'success'];
        $io     = $this->getIOMock();
        $action = $this->getActionConfigMock();
        $cmd    = CH_PATH_FILES . '/{FILE}/{MODE}';

        $io->expects($this->once())->method('getArguments')->willReturn($args);
        $io->expects($this->once())->method('write');
        $action->expects($this->once())->method('getAction')->willReturn($cmd);

        $cli = new Cli();
        $cli->execute($io, $action);
    }

    /**
     * Tests Cli::execute
     */
    public function testExecuteFailure()
    {
        $this->expectException(\Exception::class);

        $args   = [];
        $io     = $this->getIOMock();
        $action = $this->getActionConfigMock();
        $cmd    = CH_PATH_FILES . '/bin/failure';

        $io->expects($this->once())->method('getArguments')->willReturn($args);
        $action->expects($this->once())->method('getAction')->willReturn($cmd);

        $cli = new Cli();
        $cli->execute($io, $action);
    }
}
