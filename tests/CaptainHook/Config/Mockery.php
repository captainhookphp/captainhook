<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Config;

use CaptainHook\App\Config as CHConfig;

trait Mockery
{
    /**
     * Create config mock
     *
     * @param  bool   $loadedFromFile
     * @param  string $path
     * @return \CaptainHook\App\Config
     */
    public function createConfigMock(bool $loadedFromFile = false, string $path = '')
    {
        $config = $this->getMockBuilder(CHConfig::class)
                       ->disableOriginalConstructor()
                       ->getMock();

        $config->method('isLoadedFromFile')->willReturn($loadedFromFile);
        $config->method('getPath')->willReturn($path);

        return $config;
    }
}
