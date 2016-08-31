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

class BaseTestRunner extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \CaptainHook\App\Console\IO\DefaultIO
     */
    public function getIOMock()
    {
        return $this->getMockBuilder('\\CaptainHook\\App\\Console\\IO\DefaultIO')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \CaptainHook\App\Config
     */
    public function getConfigMock()
    {
        return $this->getMockBuilder('\\CaptainHook\\App\\Config')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \CaptainHook\App\Config\Hook
     */
    public function getHookConfigMock()
    {
        return $this->getMockBuilder('\\CaptainHook\\App\\Config\\Hook')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \CaptainHook\App\Config\Action
     */
    public function getActionConfigMock()
    {
        return $this->getMockBuilder('\\CaptainHook\\App\\Config\\Action')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \CaptainHook\App\Git\Repository
     */
    public function getRepositoryMock()
    {
        return $this->getMockBuilder('\\CaptainHook\\App\\Git\\Repository')
                    ->disableOriginalConstructor()
                    ->getMock();
    }
}
