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
use Composer\Package\Package;
use Composer\Script\Event;
use CaptainHook\App\Git\DummyRepo;
use PHPUnit\Framework\TestCase;

class CmdTest extends TestCase
{
    /**
     * Tests Cmd::setup
     */
    public function testSetupConfigExists()
    {
        $repo  = new DummyRepo();
        $repo->setup();

        $config = $repo->getPath() . DIRECTORY_SEPARATOR . CH::CONFIG;
        $old    = getcwd();
        chdir($repo->getPath());
        file_put_contents($config, '{}');

        $event = $this->getEventMock([]);

        Cmd::setup($event);

        $this->assertFileExists($repo->getHookDir() . DIRECTORY_SEPARATOR . 'pre-commit', 'pre-commit');
        $this->assertFileExists($repo->getHookDir() . DIRECTORY_SEPARATOR . 'pre-push', 'pre-push');
        $this->assertFileExists($repo->getHookDir() . DIRECTORY_SEPARATOR . 'commit-msg', 'commit-msg');

        $repo->cleanup();
        chdir($old);
    }

    /**
     * Tests Cmd::setup
     */
    public function testSetupNoConfig()
    {
        $repo  = new DummyRepo();
        $repo->setup();

        $config = $repo->getPath() . DIRECTORY_SEPARATOR . CH::CONFIG;
        $old    = getcwd();
        chdir($repo->getPath());

        $extra = ['captainhookconfig' => $config];
        $event = $this->getEventMock($extra);
        Cmd::setup($event);

        $this->assertFileExists($extra['captainhookconfig']);

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
        $package = $this->getMockBuilder(Package::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $package->expects($this->once())->method('getExtra')->willReturn($extra);

        $composer = $this->getMockBuilder(Composer::class)
                         ->disableOriginalConstructor()
                         ->getMock();
        $composer->expects($this->once())->method('getPackage')->willReturn($package);

        return $composer;
    }
}
