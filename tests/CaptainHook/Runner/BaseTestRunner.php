<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Runner;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO\DefaultIO;
use CaptainHook\App\Config\Hook;
use CaptainHook\App\Config\Action;
use SebastianFeldmann\Git\Repository;
use PHPUnit\Framework\TestCase;

class BaseTestRunner extends TestCase
{
    /**
     * @return \CaptainHook\App\Console\IO\DefaultIO
     */
    public function getIOMock()
    {
        return $this->getMockBuilder(DefaultIO::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \CaptainHook\App\Config
     */
    public function getConfigMock()
    {
        return $this->getMockBuilder(Config::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \CaptainHook\App\Config\Hook
     */
    public function getHookConfigMock()
    {
        return $this->getMockBuilder(Hook::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \CaptainHook\App\Config\Action
     */
    public function getActionConfigMock()
    {
        return $this->getMockBuilder(Action::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \SebastianFeldmann\Git\Repository
     */
    public function getRepositoryMock()
    {
        return $this->getMockBuilder(Repository::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }
}
