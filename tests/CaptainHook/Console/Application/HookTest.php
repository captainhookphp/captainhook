<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Console\Application;

use CaptainHook\App\CH;
use CaptainHook\App\Config;
use CaptainHook\App\Git\DummyRepo;
use \Exception;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use PHPUnit\Framework\TestCase;

class HookTest extends TestCase
{
    private $repo;

    /**
     * Create fake dummy repo
     */
    public function setUp(): void
    {
        $this->repo = new DummyRepo();
        $this->repo->setup();
    }

    /**
     * Cleanup the fake repo
     */
    public function tearDown(): void
    {
        $this->repo->cleanup();
    }

    /**
     * Tests Hook::run
     */
    public function testRun(): void
    {
        if (\defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('not tested on windows');
        }

        $config = new Config(CH_PATH_FILES . '/config/valid.json');
        $output = new NullOutput();
        $input  = new ArrayInput([
            'file' => CH_PATH_FILES . '/git/message/valid.txt',
        ]);
        $app = new Hook();
        $app->setConfigFile($config->getPath());
        $app->setRepositoryPath($this->repo->getPath());
        $app->setHook('commit-msg');
        $app->setAutoExit(false);
        $app->run($input, $output);

        $this->assertTrue(true);
    }

    /**
     * Test new error handling
     *
     * @throws \Exception
     */
    public function testHookFailure(): void
    {
        if (\defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('not tested on windows');
        }

        $output = $this->createMock(NullOutput::class);
        $output->expects($this->once())->method('isDebug')->willReturn(false);
        $output->expects($this->once())->method('isVerbose')->willReturn(true);
        $output->expects($this->exactly(2))->method('writeLn');

        $app    = new Hook();
        $config = new Config(CH_PATH_FILES . '/config/valid-but-failing.json');
        $input  = new ArrayInput([]);

        $app->setConfigFile($config->getPath());
        $app->setRepositoryPath($this->repo->getPath());
        $app->setAutoExit(false);
        $app->setHook('pre-commit');
        $app->run($input, $output);


        $this->assertTrue(true);
    }


    /**
     * Test new error handling
     *
     * @throws \Exception
     */
    public function testHookFailureWhileDebugging(): void
    {
        if (\defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('not tested on windows');
        }

        $output = $this->createMock(NullOutput::class);
        $output->expects($this->once())->method('isDebug')->willReturn(true);

        $app    = new Hook();
        $config = new Config(CH_PATH_FILES . '/config/valid-but-failing.json');
        $input  = new ArrayInput([]);

        $app->setConfigFile($config->getPath());
        $app->setRepositoryPath($this->repo->getPath());
        $app->setAutoExit(false);
        $app->setHook('pre-commit');
        $app->run($input, $output);

        $this->assertTrue(true);
    }

    /**
     * Tests Hook::executeHook
     */
    public function testRunInvalidHook(): void
    {
        $this->expectException(\Exception::class);

        $app = new Hook();
        $app->setHook('pre-foo');
    }

    /**
     * Tests Hook::executeHook
     */
    public function testRunNoHook(): void
    {
        $input  = new ArrayInput([]);
        $output = new NullOutput();
        $app    = new Hook();
        $app->setAutoExit(false);
        $exit = $app->run($input, $output);

        $this->assertTrue($exit !== 0);
    }

    /**
     * Tests Hook::getRepositoryPath
     */
    public function testGetRepositoryPath(): void
    {
        $app = new Hook();

        $this->assertEquals(getcwd(), $app->getRepositoryPath());
    }

    /**
     * Tests Hook::getConfigFile
     */
    public function testGetConfigFile(): void
    {
        $app = new Hook();

        $this->assertEquals(getcwd()  . DIRECTORY_SEPARATOR . CH::CONFIG, $app->getConfigFile());
    }

    /**
     * Tests Application::getHelp
     */
    public function testGetHelp(): void
    {
        $hook = new Hook();
        $help = $hook->getHelp();

        $this->assertStringContainsString('$$$$b ^ceeeee.  4$$ECL.F*$$$$$$$', $help);
    }
}
