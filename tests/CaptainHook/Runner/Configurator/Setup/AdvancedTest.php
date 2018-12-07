<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Runner\Configurator\Setup;

use CaptainHook\App\Runner\BaseTestRunner;

class AdvancedTest extends BaseTestRunner
{
    /**
     * Tests Advanced::configureHooks
     */
    public function testConfigureCliHook()
    {
        $io     = $this->getIOMock();
        $config = $this->getConfigMock();
        $config->expects($this->exactly(4))->method('getHookConfig')->willReturn($this->getHookConfigMock());
        $io->method('ask')->will($this->onConsecutiveCalls('y', 'y', 'echo \'foo\'', 'n'));

        $setup  = new Advanced($io);
        $setup->configureHooks($config);
    }

    /**
     * Tests Advanced::configureHooks
     */
    public function testConfigurePHPHook()
    {
        $io     = $this->getIOMock();
        $config = $this->getConfigMock();
        $config->method('getHookConfig')->willReturn($this->getHookConfigMock());
        $io->method('ask')->will($this->onConsecutiveCalls('y', 'y', '\\Foo\\Bar', 'y', 'n'));
        $io->expects($this->once())->method('askAndValidate')->willReturn('foo:bar');

        $setup  = new Advanced($io);
        $setup->configureHooks($config);
    }
}
