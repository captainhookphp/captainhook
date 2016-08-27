<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Runner\Action;

use HookMeUp\Config;
use HookMeUp\Console\IO;
use HookMeUp\Git\Repository;
use HookMeUp\Hook\Action as ActionInterface;
use HookMeUp\Runner\BaseTestRunner;

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

        $class = '\\HookMeUp\\Runner\\Action\\DummyPHPSuccess';

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

        $class = '\\HookMeUp\\Runner\\Action\\DummyPHPFailure';

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

        $class = '\\HookMeUp\\Runner\\Action\\DummyPHPError';

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

        $class = '\\HookMeUp\\Runner\\Action\\DummyNoAction';

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
     * @param  \HookMeUp\Config         $config
     * @param  \HookMeUp\Console\IO     $io
     * @param  \HookMeUp\Git\Repository $repository
     * @param  \HookMeUp\Config\Action  $action
     * @throws \HookMeUp\Exception\ActionExecution
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
     * @param  \HookMeUp\Config         $config
     * @param  \HookMeUp\Console\IO     $io
     * @param  \HookMeUp\Git\Repository $repository
     * @param  \HookMeUp\Config\Action  $action
     * @throws \HookMeUp\Exception\ActionExecution
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
     * @param  \HookMeUp\Config         $config
     * @param  \HookMeUp\Console\IO     $io
     * @param  \HookMeUp\Git\Repository $repository
     * @param  \HookMeUp\Config\Action  $action
     * @throws \HookMeUp\Exception\ActionExecution
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
