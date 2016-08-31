<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\Runner;

class BaseTestRunner extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \CaptainHook\Console\IO\DefaultIO
     */
    public function getIOMock()
    {
        return $this->getMockBuilder('\\CaptainHook\\Console\\IO\DefaultIO')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \CaptainHook\Config
     */
    public function getConfigMock()
    {
        return $this->getMockBuilder('\\CaptainHook\\Config')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \CaptainHook\Config\Hook
     */
    public function getHookConfigMock()
    {
        return $this->getMockBuilder('\\CaptainHook\\Config\\Hook')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \CaptainHook\Config\Action
     */
    public function getActionConfigMock()
    {
        return $this->getMockBuilder('\\CaptainHook\\Config\\Action')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \CaptainHook\Git\Repository
     */
    public function getRepositoryMock()
    {
        return $this->getMockBuilder('\\CaptainHook\\Git\\Repository')
                    ->disableOriginalConstructor()
                    ->getMock();
    }
}
