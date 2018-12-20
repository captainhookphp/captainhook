<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Composer;

use Composer\IO\NullIO;
use Composer\Script\Event;
use CaptainHook\App\Git\DummyRepo;
use PHPUnit\Framework\TestCase;

class CmdTest extends TestCase
{
    /**
     * Tests Cmd::configure
     */
    public function testConfigure()
    {
        $event = $this->getMockBuilder(Event::class)
                      ->disableOriginalConstructor()
                      ->getMock();
        $event->expects($this->once())->method('getIO')->willReturn(new NullIO());
        $config = sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5(__FILE__);
        Cmd::configure($event, $config);

        $this->assertFileExists($config);

        unlink($config);
    }

    /**
     * Tests Cmd::configure
     */
    public function testInstall()
    {
        $event = $this->getMockBuilder('\\Composer\\Script\\Event')
                      ->disableOriginalConstructor()
                      ->getMock();
        $event->expects($this->once())->method('getIO')->willReturn(new NullIO());

        $repo = new DummyRepo();
        $repo->setup();

        $config = $repo->getPath() . DIRECTORY_SEPARATOR . 'captainhook.json';
        $old    = getcwd();
        chdir($repo->getPath());
        file_put_contents($config, '{}');

        Cmd::install($event);

        $this->assertFileExists($repo->getHookDir() . DIRECTORY_SEPARATOR . 'pre-commit', 'pre-commit');
        $this->assertFileExists($repo->getHookDir() . DIRECTORY_SEPARATOR . 'pre-push', 'pre-push');
        $this->assertFileExists($repo->getHookDir() . DIRECTORY_SEPARATOR . 'commit-msg', 'commit-msg');

        $repo->cleanup();
        chdir($old);
    }
}
