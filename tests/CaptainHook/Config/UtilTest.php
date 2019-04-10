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

use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase
{
    /**
     * Tests Util::validateJsonConfiguration
     */
    public function testEnabledMissing()
    {
        $this->expectException(\Exception::class);

        Util::validateJsonConfiguration(['pre-commit' => ['actions' => []]]);
    }

    /**
     * Tests Util::validateJsonConfiguration
     */
    public function testActionsMissing()
    {
        $this->expectException(\Exception::class);

        Util::validateJsonConfiguration(['pre-commit' => ['enabled' => true]]);
    }

    /**
     * Tests Util::validateJsonConfiguration
     */
    public function testActionsNoArray()
    {
        $this->expectException(\Exception::class);

        Util::validateJsonConfiguration(['pre-commit' => ['enabled' => true, 'actions' => false]]);
    }

    /**
     * Tests Util::validateJsonConfiguration
     */
    public function testActionMissing()
    {
        $this->expectException(\Exception::class);

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
     */
    public function testActionEmpty()
    {
        $this->expectException(\Exception::class);

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
    public function testConditionExecMissing()
    {
        $this->expectException(\Exception::class);

        Util::validateJsonConfiguration(
            [
                'pre-commit' => [
                    'enabled' => true,
                    'actions' => [
                        [
                            'action'    => 'foo',
                            'conditions' => [
                                [
                                    'args' => [
                                        'foo' => 'fiz',
                                        'bar' => 'baz'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * Tests Util::validateJsonConfiguration
     */
    public function testConditionArgsNoArray()
    {
        $this->expectException(\Exception::class);

        Util::validateJsonConfiguration(
            [
                'pre-commit' => [
                    'enabled' => true,
                    'actions' => [
                        [
                            'action'    => 'foo',
                            'conditions' => [
                                [
                                    'exec' => '\\Foo',
                                    'args' => 'fooBarBaz'
                                ]
                            ]
                        ]
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

    /**
     * Tests Util::validateJsonConfiguration
     */
    public function testValidWithCondition()
    {
        Util::validateJsonConfiguration(
            [
                'pre-commit' => [
                    'enabled' => true,
                    'actions' => [
                        [
                            'action'    => 'foo',
                            'conditions' => [
                                [
                                    'exec' => '\\Fiz\\Baz',
                                    'args' => [
                                        'foo' => 'fiz',
                                        'bar' => 'baz'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        $this->assertTrue(true);
    }
}
