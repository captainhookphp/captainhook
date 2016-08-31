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

use CaptainHook\Config;
use CaptainHook\Console\IO;
use CaptainHook\Git\Repository;
use CaptainHook\Hook\Action as ActionInterface;
use CaptainHook\Runner\BaseTestRunner;

class PHPTest extends BaseTestRunner
{
    /**
     * Tests PHP::execute
     */
    public function testExecuteSuccess()
    {
        $config = $this->getConfigMock();
        $io     = $this->getIOMock();
        $repo   = $this->getRepositoryMock();
        $action = $this->getActionConfigMock();

        $class = '\\CaptainHook\\Runner\\Action\\DummyPHPSuccess';

        $action->expects($this->once())->method('getAction')->willReturn($class);

        $php = new PHP();
        $php->execute($config, $io, $repo, $action);
    }

    /**
     * Tests PHP::execute
     *
     * @expectedException \Exception
     */
    public function testExecuteFailure()
    {
        $config = $this->getConfigMock();
        $io     = $this->getIOMock();
        $repo   = $this->getRepositoryMock();
        $action = $this->getActionConfigMock();

        $class = '\\CaptainHook\\Runner\\Action\\DummyPHPFailure';

        $action->expects($this->once())->method('getAction')->willReturn($class);

        $php = new PHP();
        $php->execute($config, $io, $repo, $action);
    }

    /**
     * Tests PHP::execute
     *
     * @expectedException \Exception
     */
    public function testExecuteError()
    {
        $config = $this->getConfigMock();
        $io     = $this->getIOMock();
        $repo   = $this->getRepositoryMock();
        $action = $this->getActionConfigMock();

        $class = '\\CaptainHook\\Runner\\Action\\DummyPHPError';

        $action->expects($this->once())->method('getAction')->willReturn($class);

        $php = new PHP();
        $php->execute($config, $io, $repo, $action);
    }

    /**
     * Tests PHP::execute
     *
     * @expectedException \Exception
     */
    public function testExecuteNoAction()
    {
        $config = $this->getConfigMock();
        $io     = $this->getIOMock();
        $repo   = $this->getRepositoryMock();
        $action = $this->getActionConfigMock();

        $class = '\\CaptainHook\\Runner\\Action\\DummyNoAction';

        $action->expects($this->once())->method('getAction')->willReturn($class);

        $php = new PHP();
        $php->execute($config, $io, $repo, $action);
    }
}

class DummyPHPSuccess implements ActionInterface
{
    /**
     * Execute the configured action.
     *
     * @param  \CaptainHook\Config         $config
     * @param  \CaptainHook\Console\IO     $io
     * @param  \CaptainHook\Git\Repository $repository
     * @param  \CaptainHook\Config\Action  $action
     * @throws \CaptainHook\Exception\ActionExecution
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action)
    {
        // do something fooish
    }
}

class DummyPHPFailure implements ActionInterface
{
    /**
     * Execute the configured action.
     *
     * @param  \CaptainHook\Config         $config
     * @param  \CaptainHook\Console\IO     $io
     * @param  \CaptainHook\Git\Repository $repository
     * @param  \CaptainHook\Config\Action  $action
     * @throws \CaptainHook\Exception\ActionExecution
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action)
    {
        throw new \RuntimeException('Execution failed');
    }
}

class DummyPHPError implements ActionInterface
{
    /**
     * Execute the configured action.
     *
     * @param  \CaptainHook\Config         $config
     * @param  \CaptainHook\Console\IO     $io
     * @param  \CaptainHook\Git\Repository $repository
     * @param  \CaptainHook\Config\Action  $action
     * @throws \CaptainHook\Exception\ActionExecution
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action)
    {
        str_pos();
    }
}

class DummyNoAction {
    /**
     * Barish
     */
    public function dummy()
    {
        // do something barish
    }
}
