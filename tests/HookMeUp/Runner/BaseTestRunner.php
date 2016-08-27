<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Runner;

class BaseTestRunner extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \HookMeUp\Console\IO\DefaultIO
     */
    public function getIOMock()
    {
        return $this->getMockBuilder('\\HookMeUp\\Console\\IO\DefaultIO')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \HookMeUp\Config
     */
    public function getConfigMock()
    {
        return $this->getMockBuilder('\\HookMeUp\\Config')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \HookMeUp\Config\Hook
     */
    public function getHookConfigMock()
    {
        return $this->getMockBuilder('\\HookMeUp\\Config\\Hook')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \HookMeUp\Config\Action
     */
    public function getActionConfigMock()
    {
        return $this->getMockBuilder('\\HookMeUp\\Config\\Action')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \HookMeUp\Git\Repository
     */
    public function getRepositoryMock()
    {
        return $this->getMockBuilder('\\HookMeUp\\Git\\Repository')
                    ->disableOriginalConstructor()
                    ->getMock();
    }
}
