<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Runner\Configurator\Setup;

use SebastianFeldmann\CaptainHook\Runner\BaseTestRunner;

class ExpressTest extends BaseTestRunner
{
    /**
     * Tests Express::configureHooks
     */
    public function testConfigureExpress()
    {
        $io     = $this->getIOMock();
        $config = $this->getConfigMock();
        $config->expects($this->exactly(2))->method('getHookConfig')->willReturn($this->getHookConfigMock());
        $io->method('ask')->will($this->onConsecutiveCalls('y', 'y', 'y', 'phpunit', 'y', 'phpcs'));

        $setup  = new Express($io);
        $setup->configureHooks($config);
    }
}
