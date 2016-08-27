<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Console\Application;

use HookMeUp\Config;
use HookMeUp\Git\DummyRepo;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class HookTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Hook::run
     */
    public function testRun()
    {
        $config = new Config(HMU_PATH_FILES . '/config/valid.json');
        $repo   = new DummyRepo();
        $output = new NullOutput();
        $input  = new ArrayInput([
            'file' => HMU_PATH_FILES . '/git/message/valid.txt',
        ]);
        $app = new Hook();
        $app->useConfigFile($config);
        $app->useRepository($repo->getPath());
        $app->executeHook('commit-msg');
        $app->setAutoExit(false);
        $app->run($input, $output);

        $repo->cleanup();
    }

    /**
     * Tests Hook::executeHook
     *
     * @expectedException \Exception
     */
    public function testRunInvalidHook()
    {
        $app = new Hook();
        $app->executeHook('pre-foo');
    }

    /**
     * Tests Hook::getRepositoryPath
     */
    public function testGetRepositoryPath()
    {
        $app = new Hook();
        $this->assertEquals(getcwd(), $app->getRepositoryPath());
    }

    /**
     * Tests Hook::getConfigFile
     */
    public function testGetConfigFile()
    {
        $app = new Hook();
        $this->assertEquals(getcwd()  . '/hookmeup.json', $app->getConfigFile());
    }

    /**
     * Tests Application::getHelp
     */
    public function testGetHelp()
    {
        $hook = new Hook();
        $help = $hook->getHelp();

        $this->assertTrue(
            (bool)strpos(
                $help,
                '\\ \\ \\ \\ \\/\\ \\L\\ \\/\\ \\L\\ \\ \\ \\\\`\\ /\\ \\/\\ \\/\\ \\/\\  __/\\ \\ \\_\\ \\ \\ \\L\\ \\'
            )
        );
    }
}
