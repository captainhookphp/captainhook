<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner\Config\Setup;

use CaptainHook\App\Config\Mockery as ConfigMockery;
use CaptainHook\App\Console\IO\Mockery as IOMockery;
use CaptainHook\App\Mockery as CHMockery;
use PHPUnit\Framework\TestCase;

class AdvancedTest extends TestCase
{
    use ConfigMockery;
    use IOMockery;
    use CHMockery;

    /**
     * Tests Advanced::configureHooks
     *
     * @throws \Exception
     */
    public function testConfigureCliHook(): void
    {
        $io     = $this->createIOMock();
        $config = $this->createConfigMock();
        $config->expects($this->exactly(9))->method('getHookConfig')->willReturn($this->createHookConfigMock());
        $io->method('ask')->will(
            $this->onConsecutiveCalls(
                ...['y', 'y', 'echo \'foo\'', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n']
            )
        );

        $setup  = new Advanced($io);
        $setup->configureHooks($config);
    }

    /**
     * Tests Advanced::configureHooks
     *
     * @throws \Exception
     */
    public function testConfigurePHPHook(): void
    {
        $io     = $this->createIOMock();
        $config = $this->createConfigMock();
        $config->method('getHookConfig')->willReturn($this->createHookConfigMock());
        $io->method('ask')->will(
            $this->onConsecutiveCalls(
                ...['y', 'y', '\\Foo\\Bar', 'y', 'foo:bar', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n', 'n']
            )
        );
        $io->expects($this->once())->method('askAndValidate')->willReturn('foo:bar');

        $setup  = new Advanced($io);
        $setup->configureHooks($config);
    }
}
