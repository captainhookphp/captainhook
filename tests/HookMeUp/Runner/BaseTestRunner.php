<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\App\Runner;

class BaseTestRunner extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \HookMeUp\App\Console\IO\DefaultIO
     */
    public function getIOMock()
    {
        return $this->getMockBuilder('\\HookMeUp\\App\\Console\\IO\DefaultIO')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \HookMeUp\App\Config
     */
    public function getConfigMock()
    {
        return $this->getMockBuilder('\\HookMeUp\\App\\Config')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \HookMeUp\App\Config\Hook
     */
    public function getHookConfigMock()
    {
        return $this->getMockBuilder('\\HookMeUp\\App\\Config\\Hook')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \HookMeUp\App\Config\Action
     */
    public function getActionConfigMock()
    {
        return $this->getMockBuilder('\\HookMeUp\\App\\Config\\Action')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \HookMeUp\App\Git\Repository
     */
    public function getRepositoryMock()
    {
        return $this->getMockBuilder('\\HookMeUp\\App\\Git\\Repository')
                    ->disableOriginalConstructor()
                    ->getMock();
    }
}
