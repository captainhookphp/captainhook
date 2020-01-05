<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner\Action;

use CaptainHook\App\Config\Mockery as ConfigMockery;
use CaptainHook\App\Console\IO\Mockery as IOMockery;
use CaptainHook\App\Mockery as CHMockery;
use Exception;
use PHPUnit\Framework\TestCase;

class PHPTest extends TestCase
{
    use ConfigMockery;
    use IOMockery;
    use CHMockery;

    /**
     * Tests PHP::execute
     */
    public function testExecuteSuccess(): void
    {
        $config = $this->createConfigMock();
        $io     = $this->createIOMock();
        $repo   = $this->createRepositoryMock();
        $action = $this->createActionConfigMock();
        $class  = DummyPHPSuccess::class;

        $action->expects($this->once())->method('getAction')->willReturn($class);

        $php = new PHP('pre-commit');
        $php->execute($config, $io, $repo, $action);
    }

    /**
     * Tests PHP::execute
     */
    public function testExecuteConstraintApplicable(): void
    {
        $config = $this->createConfigMock();
        $io     = $this->createIOMock();
        $repo   = $this->createRepositoryMock();
        $action = $this->createActionConfigMock();
        $class  = DummyPHPConstraint::class;

        $action->expects($this->once())->method('getAction')->willReturn($class);

        $php = new PHP('pre-commit');
        $php->execute($config, $io, $repo, $action);
    }

    /**
     * Tests PHP::execute
     */
    public function testExecuteConstraintNotApplicable(): void
    {
        $config = $this->createConfigMock();
        $io     = $this->createIOMock();
        $repo   = $this->createRepositoryMock();
        $action = $this->createActionConfigMock();
        $class  = DummyPHPConstraint::class;

        $action->expects($this->once())->method('getAction')->willReturn($class);

        $php = new PHP('pre-push');
        $php->execute($config, $io, $repo, $action);
    }

    /**
     * Tests PHP::execute
     *
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    public function testExecuteFailure(): void
    {
        $this->expectException(Exception::class);

        $config = $this->createConfigMock();
        $io     = $this->createIOMock();
        $repo   = $this->createRepositoryMock();
        $action = $this->createActionConfigMock();
        $class  = DummyPHPFailure::class;

        $action->expects($this->once())->method('getAction')->willReturn($class);

        $php = new PHP('pre-commit');
        $php->execute($config, $io, $repo, $action);
    }

    /**
     * Tests PHP::execute
     *
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    public function testExecuteError(): void
    {
        $this->expectException(Exception::class);

        $config = $this->createConfigMock();
        $io     = $this->createIOMock();
        $repo   = $this->createRepositoryMock();
        $action = $this->createActionConfigMock();
        $class  = DummyPHPError::class;

        $action->expects($this->once())->method('getAction')->willReturn($class);

        $php = new PHP('pre-commit');
        $php->execute($config, $io, $repo, $action);
    }

    /**
     * Tests PHP::execute
     *
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    public function testExecuteNoAction(): void
    {
        $this->expectException(Exception::class);

        $config = $this->createConfigMock();
        $io     = $this->createIOMock();
        $repo   = $this->createRepositoryMock();
        $action = $this->createActionConfigMock();
        $class  = DummyNoAction::class;

        $action->expects($this->once())->method('getAction')->willReturn($class);

        $php = new PHP('pre-commit');
        $php->execute($config, $io, $repo, $action);
    }

    /**
     * Tests PHP::executeStatic
     *
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    public function testExecuteStaticClassNotFound(): void
    {
        $this->expectException(Exception::class);

        $config = $this->createConfigMock();
        $io     = $this->createIOMock();
        $repo   = $this->createRepositoryMock();
        $action = $this->createActionConfigMock();

        $class = '\\Fiz::baz';

        $action->expects($this->once())->method('getAction')->willReturn($class);

        $php = new PHP('pre-commit');
        $php->execute($config, $io, $repo, $action);
    }

    /**
     * Tests PHP::executeStatic
     *
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    public function testExecuteStaticMethodNotFound(): void
    {
        $this->expectException(Exception::class);

        $config = $this->createConfigMock();
        $io     = $this->createIOMock();
        $repo   = $this->createRepositoryMock();
        $action = $this->createActionConfigMock();

        $class = '\\CaptainHook\\App\\Runner\\Action\\DummyNoAction::foo';

        $action->expects($this->once())->method('getAction')->willReturn($class);

        $php = new PHP('pre-commit');
        $php->execute($config, $io, $repo, $action);
    }


    /**
     * Tests PHP::executeStatic
     *
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    public function testExecuteStaticSuccess(): void
    {
        $config = $this->createConfigMock();
        $io     = $this->createIOMock();
        $repo   = $this->createRepositoryMock();
        $action = $this->createActionConfigMock();

        $class = '\\CaptainHook\\App\\Runner\\Action\\DummyPHPSuccess::executeStatic';

        $action->expects($this->once())->method('getAction')->willReturn($class);

        $php = new PHP('pre-commit');
        $php->execute($config, $io, $repo, $action);
    }
}
