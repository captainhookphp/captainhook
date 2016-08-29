<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Composer;

use Composer\IO\NullIO;

class CmdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Cmd::configure
     */
    public function testConfigure()
    {
        $event = $this->getMockBuilder('\\Composer\\Script\\Event')
                      ->disableOriginalConstructor()
                      ->getMock();
        $event->expects($this->once())->method('getIO')->willReturn(new NullIO());
        $config = sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5(__FILE__);
        Cmd::configure($event, $config);

        $this->assertTrue(file_exists($config));

        unlink($config);
    }
}
