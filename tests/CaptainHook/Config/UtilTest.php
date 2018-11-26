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

class UtilTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests Util::validateJsonConfiguration
     *
     * @expectedException \Exception
     */
    public function testInvalidHookName()
    {
        Util::validateJsonConfiguration(['pre-what-ever' => []]);
    }

    /**
     * Tests Util::validateJsonConfiguration
     *
     * @expectedException \Exception
     */
    public function testEnabledMissing()
    {
        Util::validateJsonConfiguration(['pre-commit' => ['actions' => []]]);
    }

    /**
     * Tests Util::validateJsonConfiguration
     *
     * @expectedException \Exception
     */
    public function testActionsMissing()
    {
        Util::validateJsonConfiguration(['pre-commit' => ['enabled' => true]]);
    }

    /**
     * Tests Util::validateJsonConfiguration
     *
     * @expectedException \Exception
     */
    public function testActionsNoArray()
    {
        Util::validateJsonConfiguration(['pre-commit' => ['enabled' => true, 'actions' => false]]);
    }

    /**
     * Tests Util::validateJsonConfiguration
     *
     * @expectedException \Exception
     */
    public function testActionMissing()
    {
        Util::validateJsonConfiguration(
            [
                'pre-commit' => [
                    'enabled' => true,
                    'actions' => [
                        ['options' => []]
                    ]
                ]
            ]
        );
        $this->assertTrue(true);
    }

    /**
     * Tests Util::validateJsonConfiguration
     *
     * @expectedException \Exception
     */
    public function testActionEmpty()
    {
        Util::validateJsonConfiguration(
            [
                'pre-commit' => [
                    'enabled' => true,
                    'actions' => [
                        ['action'  => '']
                    ]
                ]
            ]
        );
    }

    /**
     * Tests Util::validateJsonConfiguration
     */
    public function testValid()
    {
        Util::validateJsonConfiguration(
            [
                'pre-commit' => [
                    'enabled' => true,
                    'actions' => [
                        ['action'  => 'foo']
                    ]
                ]
            ]
        );
        $this->assertTrue(true);
    }
}
