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

use CaptainHook\App\Runner\BaseTestRunner;
use CaptainHook\App\Runner\Action\DummyPHPSuccess;
use CaptainHook\App\Runner\Action\DummyPHPFailure;
use CaptainHook\App\Runner\Action\DummyPHPError;
use CaptainHook\App\Runner\Action\DummyNoAction;

class PHPTest extends BaseTestRunner
{
    /**
     * Tests PHP::execute
     */
    public function testExecuteSuccess(): void
    {
        $config = $this->getConfigMock();
        $io     = $this->getIOMock();
        $repo   = $this->getRepositoryMock();
        $action = $this->getActionConfigMock();
        $class  = DummyPHPSuccess::class;

        $action->expects($this->once())->method('getAction')->willReturn($class);

        $php = new PHP();
        $php->execute($config, $io, $repo, $action);
    }

    /**
     * Tests PHP::execute
     */
    public function testExecuteFailure(): void
    {
        $this->expectException(\Exception::class);

        $config = $this->getConfigMock();
        $io     = $this->getIOMock();
        $repo   = $this->getRepositoryMock();
        $action = $this->getActionConfigMock();
        $class  = DummyPHPFailure::class;

        $action->expects($this->once())->method('getAction')->willReturn($class);

        $php = new PHP();
        $php->execute($config, $io, $repo, $action);
    }

    /**
     * Tests PHP::execute
     */
    public function testExecuteError(): void
    {
        $this->expectException(\Exception::class);

        $config = $this->getConfigMock();
        $io     = $this->getIOMock();
        $repo   = $this->getRepositoryMock();
        $action = $this->getActionConfigMock();
        $class  = DummyPHPError::class;

        $action->expects($this->once())->method('getAction')->willReturn($class);

        $php = new PHP();
        $php->execute($config, $io, $repo, $action);
    }

    /**
     * Tests PHP::execute
     */
    public function testExecuteNoAction(): void
    {
        $this->expectException(\Exception::class);

        $config = $this->getConfigMock();
        $io     = $this->getIOMock();
        $repo   = $this->getRepositoryMock();
        $action = $this->getActionConfigMock();
        $class  = DummyNoAction::class;

        $action->expects($this->once())->method('getAction')->willReturn($class);

        $php = new PHP();
        $php->execute($config, $io, $repo, $action);
    }

    /**
     * Tests PHP::executeStatic
     */
    public function testExecuteStaticClassNotFound(): void
    {
        $this->expectException(\Exception::class);

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
     */
    public function testExecuteStaticMethodNotFound(): void
    {
        $this->expectException(\Exception::class);
        
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
    public function testExecuteStaticSuccess(): void
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
