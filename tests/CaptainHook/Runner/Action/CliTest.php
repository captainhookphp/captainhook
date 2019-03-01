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
        $io     = $this->getIOMock();
        $action = $this->getActionConfigMock();
        $args   = new Options([]);
        $cmd    = CH_PATH_FILES . '/bin/success';

        $io->expects($this->once())->method('write');
        $action->expects($this->once())->method('getAction')->willReturn($cmd);

        $cli = new Cli();
        $cli->execute($io, $action, $args);
    }

    /**
     * Tests Cli::execute
     */
    public function testExecuteSuccessWithReplacements()
    {
        $io     = $this->getIOMock();
        $action = $this->getActionConfigMock();
        $args   = new Options(['file' => 'bin', 'mode' => 'success']);
        $cmd    = CH_PATH_FILES . '/{FILE}/{MODE}';

        $io->expects($this->once())->method('write');
        $action->expects($this->once())->method('getAction')->willReturn($cmd);

        $cli = new Cli();
        $cli->execute($io, $action, $args);
    }

    /**
     * Tests Cli::execute
     */
    public function testExecuteFailure()
    {
        $this->expectException(\Exception::class);

        $io     = $this->getIOMock();
        $action = $this->getActionConfigMock();
        $args   = new Options([]);
        $cmd    = CH_PATH_FILES . '/bin/failure';

        $action->expects($this->once())->method('getAction')->willReturn($cmd);

        $cli = new Cli();
        $cli->execute($io, $action, $args);
    }
}
