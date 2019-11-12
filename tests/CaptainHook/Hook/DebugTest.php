<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook;

use PHPUnit\Framework\TestCase;
use CaptainHook\App\Config\Action;
use CaptainHook\App\Config\Mockery as ConfigMockery;
use CaptainHook\App\Console\IO\Mockery as IOMockery;
use CaptainHook\App\Mockery as CHMockery;

class DebugTest extends TestCase
{
    use ConfigMockery;
    use IOMockery;
    use CHMockery;

    /**
     * Tests Debug::execute
     */
    public function testExecute(): void
    {
        $this->expectException(\Exception::class);

        $config       = $this->createConfigMock(true);
        $io           = $this->createIOMock();
        $repository   = $this->createRepositoryMock();
        $infoOperator = $this->createGitInfoOperator('1.0.0');
        $action       = new Action('\\' . Debug::class);

        $io->expects($this->once())->method('getArguments')->willReturn(['foo' => 'bar']);
        $io->expects($this->exactly(3))->method('write');
        $repository->expects($this->once())->method('getInfoOperator')->willReturn($infoOperator);

        $debug = new Debug();
        $debug->execute($config, $io, $repository, $action);
    }
}
