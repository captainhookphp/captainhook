<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Runner;

class BaseTestRunner extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \sebastianfeldmann\CaptainHook\Console\IO\DefaultIO
     */
    public function getIOMock()
    {
        return $this->getMockBuilder('\\sebastianfeldmann\\CaptainHook\\Console\\IO\DefaultIO')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \sebastianfeldmann\CaptainHook\Config
     */
    public function getConfigMock()
    {
        return $this->getMockBuilder('\\sebastianfeldmann\\CaptainHook\\Config')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \sebastianfeldmann\CaptainHook\Config\Hook
     */
    public function getHookConfigMock()
    {
        return $this->getMockBuilder('\\sebastianfeldmann\\CaptainHook\\Config\\Hook')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \sebastianfeldmann\CaptainHook\Config\Action
     */
    public function getActionConfigMock()
    {
        return $this->getMockBuilder('\\sebastianfeldmann\\CaptainHook\\Config\\Action')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \sebastianfeldmann\CaptainHook\Git\Repository
     */
    public function getRepositoryMock()
    {
        return $this->getMockBuilder('\\sebastianfeldmann\\CaptainHook\\Git\\Repository')
                    ->disableOriginalConstructor()
                    ->getMock();
    }
}
