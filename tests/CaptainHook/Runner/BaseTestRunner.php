<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Runner;

class BaseTestRunner extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \SebastianFeldmann\CaptainHook\Console\IO\DefaultIO
     */
    public function getIOMock()
    {
        return $this->getMockBuilder('\\SebastianFeldmann\\CaptainHook\\Console\\IO\DefaultIO')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \SebastianFeldmann\CaptainHook\Config
     */
    public function getConfigMock()
    {
        return $this->getMockBuilder('\\SebastianFeldmann\\CaptainHook\\Config')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \SebastianFeldmann\CaptainHook\Config\Hook
     */
    public function getHookConfigMock()
    {
        return $this->getMockBuilder('\\SebastianFeldmann\\CaptainHook\\Config\\Hook')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \SebastianFeldmann\CaptainHook\Config\Action
     */
    public function getActionConfigMock()
    {
        return $this->getMockBuilder('\\SebastianFeldmann\\CaptainHook\\Config\\Action')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \SebastianFeldmann\Git\Repository
     */
    public function getRepositoryMock()
    {
        return $this->getMockBuilder('\\SebastianFeldmann\\Git\\Repository')
                    ->disableOriginalConstructor()
                    ->getMock();
    }
}
