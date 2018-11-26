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

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Hook\Action as ActionInterface;
use CaptainHook\App\Runner\BaseTestRunner;
use SebastianFeldmann\Git\Repository;

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

        $class = '\\CaptainHook\\App\\Runner\\Action\\DummyPHPSuccess';

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

        $class = '\\CaptainHook\\App\\Runner\\Action\\DummyPHPFailure';

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

        $class = '\\CaptainHook\\App\\Runner\\Action\\DummyPHPError';

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

        $class = '\\CaptainHook\\App\\Runner\\Action\\DummyNoAction';

        $action->expects($this->once())->method('getAction')->willReturn($class);

        $php = new PHP();
        $php->execute($config, $io, $repo, $action);
    }

    /**
     * Tests PHP::executeStatic
     *
     * @expectedException \Exception
     */
    public function testExecuteStaticClassNotFound()
    {
        $config = $this->getConfigMock();
        $io     = $this->getIOMock();
        $repo   = $this->getRepositoryMock();
        $action = $this->getActionConfigMock();

        $class = '\\Fiz::baz';

        $action->expects($this->once())->method('getAction')->willReturn($class);

        $php = new PHP();
        $php->execute($config, $io, $repo, $action);
    }

    /**
     * Tests PHP::executeStatic
     *
     * @expectedException \Exception
     */
    public function testExecuteStaticMethodNotFound()
    {
        $config = $this->getConfigMock();
        $io     = $this->getIOMock();
        $repo   = $this->getRepositoryMock();
        $action = $this->getActionConfigMock();

        $class = '\\CaptainHook\\App\\Runner\\Action\\DummyNoAction::foo';

        $action->expects($this->once())->method('getAction')->willReturn($class);

        $php = new PHP();
        $php->execute($config, $io, $repo, $action);
    }


    /**
     * Tests PHP::executeStatic
     */
    public function testExecuteStaticSuccess()
    {
        $config = $this->getConfigMock();
        $io     = $this->getIOMock();
        $repo   = $this->getRepositoryMock();
        $action = $this->getActionConfigMock();

        $class = '\\CaptainHook\\App\\Runner\\Action\\DummyPHPSuccess::executeStatic';

        $action->expects($this->once())->method('getAction')->willReturn($class);

        $php = new PHP();
        $php->execute($config, $io, $repo, $action);
    }
}

class DummyPHPSuccess implements ActionInterface
{
    /**
     * Static action execution
     */
    public static function executeStatic()
    {
        // do something fooish statically
    }

    /**
     * Execute the configured action.
     *
     * @param  \CaptainHook\App\Config         $config
     * @param  \CaptainHook\App\Console\IO     $io
     * @param  \SebastianFeldmann\Git\Repository             $repository
     * @param  \CaptainHook\App\Config\Action  $action
     * @throws \CaptainHook\App\Exception\ActionFailed
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
     * @param  \CaptainHook\App\Config         $config
     * @param  \CaptainHook\App\Console\IO     $io
     * @param  \SebastianFeldmann\Git\Repository             $repository
     * @param  \CaptainHook\App\Config\Action  $action
     * @throws \CaptainHook\App\Exception\ActionFailed
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
     * @param  \CaptainHook\App\Config         $config
     * @param  \CaptainHook\App\Console\IO     $io
     * @param  \SebastianFeldmann\Git\Repository             $repository
     * @param  \CaptainHook\App\Config\Action  $action
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action)
    {
        str_pos();
    }
}

class DummyNoAction
{
    /**
     * Barish
     */
    public function dummy()
    {
        // do something barish
    }
}
