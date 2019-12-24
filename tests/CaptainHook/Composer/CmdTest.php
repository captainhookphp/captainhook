<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Composer;

use CaptainHook\App\CH;
use CaptainHook\App\Git\DummyRepo;
use Composer\Composer;
use Composer\IO\NullIO;
use Composer\Package\Package;
use Composer\Script\Event;
use Exception;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class CmdTest extends TestCase
{
    /**
     * Tests Cmd::setup
     *
     * @throws \Exception
     */
    public function testSetupConfigExists(): void
    {
        $cwd  = getcwd();
        $repo = new DummyRepo(
            [],
            [
                'composer'   => [
                    'autoload.php' => ''
                ],
                CH::CONFIG => '{"config":{"bootstrap": "composer/autoload.php"}}'
            ]
        );

        $config = $repo->getRoot() . '/' . CH::CONFIG;
        $event  = $this->createEventMock(['captainhook-config' => $config]);

        chdir(CH_PATH_FILES . '/composer');

        Cmd::setup($event);
        $this->assertFileExists($repo->getHookDir() . '/pre-commit');
        $this->assertFileExists($repo->getHookDir() . '/pre-push');
        $this->assertFileExists($repo->getHookDir() . '/commit-msg');

        chdir($cwd);
    }

    /**
     * Tests Cmd::setup
     *
     * @throws \Exception
     */
    public function testSubDirectoryInstall(): void
    {
        $cwd  = getcwd();
        $repo = new DummyRepo(
            [],
            [
                'app' => [
                    'custom' => [
                        'autoload.php' => ''
                    ],
                    CH::CONFIG => '{"config":{"bootstrap": "custom/autoload.php"}}'
                ]
            ]
        );

        $config = $repo->getRoot() . '/app/' . CH::CONFIG;
        $event  = $this->createEventMock(['captainhook-config' => $config]);

        chdir(CH_PATH_FILES . '/composer');

        Cmd::setup($event);
        $this->assertFileExists($repo->getHookDir() . '/pre-commit');
        $this->assertFileExists($repo->getHookDir() . '/pre-push');
        $this->assertFileExists($repo->getHookDir() . '/commit-msg');

        chdir($cwd);
    }

    /**
     * Tests Cmd::setup
     *
     * @throws \Exception
     */
    public function testGitDirectoryNotFound(): void
    {
        $this->expectException(Exception::class);

        $fakeConfig = vfsStream::setup('root', null, [CH::CONFIG => '{}']);
        $event      = $this->createEventMock(['captainhook-config' => $fakeConfig->url() . '/' . CH::CONFIG]);

        Cmd::setup($event);
    }

    /**
     * Tests Cmd::setup
     *
     * @throws \Exception
     */
    public function testSetupNoConfig(): void
    {
        $cwd    = getcwd();
        $repo   = new DummyRepo(
            [],
            [
                'vendor' => [
                    'autoload.php' => ''
                ]
            ]
        );
        $config = $repo->getRoot() . '/' . CH::CONFIG;
        $extra  = ['captainhook-config' => $config];
        $event  = $this->createEventMock($extra);

        chdir(CH_PATH_FILES . '/composer');

        Cmd::setup($event);
        $this->assertFileExists($extra['captainhook-config']);

        chdir($cwd);
    }

    /**
     * Create event mock to test composer scripts
     *
     * @param  array $extra
     * @return \Composer\Script\Event&\PHPUnit\Framework\MockObject\MockObject
     */
    private function createEventMock(array $extra = [])
    {
        $composer = $this->createComposerMock($extra);
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
     * @return \Composer\Composer&\PHPUnit\Framework\MockObject\MockObject
     */
    private function createComposerMock(array $extra = [])
    {
        $package = $this->getMockBuilder(Package::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $package->expects($this->atLeast(1))->method('getExtra')->willReturn($extra);

        $composer = $this->getMockBuilder(Composer::class)
                         ->disableOriginalConstructor()
                         ->getMock();
        $composer->expects($this->atLeast(1))->method('getPackage')->willReturn($package);

        return $composer;
    }
}
