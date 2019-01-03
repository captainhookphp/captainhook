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

use CaptainHook\App\CH;
use Composer\Composer;
use Composer\Config;
use Composer\IO\NullIO;
use Composer\Script\Event;
use CaptainHook\App\Git\DummyRepo;
use PHPUnit\Framework\TestCase;

class CmdTest extends TestCase
{
    /**
     * Tests Cmd::configure
     */
    public function testConfigureConfigExists()
    {
        $extra = ['captainhookconfig' => CH_PATH_FILES . '/config/valid.json'];
        $event = $this->getEventMock($extra);

        Cmd::configure($event);

        $this->assertFileExists($extra['captainhookconfig']);
    }

    /**
     * Tests Cmd::configure
     */
    public function testConfigureNoConfig()
    {
        $extra = ['captainhookconfig' => sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5(__FILE__)];
        $event = $this->getEventMock($extra);
        Cmd::configure($event);

        $this->assertFileExists($extra['captainhookconfig']);

        unlink($extra['captainhookconfig']);
    }

    /**
     * Tests Cmd::configure
     */
    public function testInstall()
    {
        $event = $this->getEventMock();
        $repo  = new DummyRepo();
        $repo->setup();

        $config = $repo->getPath() . DIRECTORY_SEPARATOR . CH::CONFIG;
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

    /**
     * Create event mock to test composer scripts
     *
     * @param  array $extra
     * @return \Composer\Script\Event
     */
    private function getEventMock(array $extra = [])
    {
        $composer = $this->getComposerMock($extra);
        $event    = $this->getMockBuilder(Event::class)
                         ->disableOriginalConstructor()
                         ->getMock();

        $event->expects($this->once())->method('getIO')->willReturn(new NullIO());
        $event->expects($this->once())->method('getComposer')->willReturn($composer);

        return $event;
    }

    /**
     * Create composer mock to return composer extra config
     *
     * @param  array $extra
     * @return \Composer\Composer
     */
    private function getComposerMock(array $extra = [])
    {
        $config = $this->getMockBuilder(Config::class)
                       ->disableOriginalConstructor()
                       ->getMock();
        $config->expects($this->once())->method('get')->willReturn($extra);

        $composer = $this->getMockBuilder(Composer::class)
                         ->disableOriginalConstructor()
                         ->getMock();
        $composer->expects($this->once())->method('getConfig')->willReturn($config);

        return $composer;
    }
}
