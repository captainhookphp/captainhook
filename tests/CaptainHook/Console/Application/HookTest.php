<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\Console\Application;

use CaptainHook\Config;
use CaptainHook\Git\DummyRepo;
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
        $app->setConfigFile($config);
        $app->setRepositoryPath($repo->getPath());
        $app->setHook('commit-msg');
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
        $app->setHook('pre-foo');
    }

    /**
     * Tests Hook::executeHook
     */
    public function testRunNoHook()
    {
        $input  = new ArrayInput([]);
        $output = new NullOutput();
        $app    = new Hook();
        $app->setAutoExit(false);
        $exit = $app->run($input, $output);

        $this->assertTrue($exit != 0);
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
        $this->assertEquals(getcwd()  . '/captainhook.json', $app->getConfigFile());
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
                '$$$$b ^ceeeee.  4$$ECL.F*$$$$$$$'
            )
        );
    }
}
