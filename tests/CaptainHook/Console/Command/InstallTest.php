<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Console\Command;

use CaptainHook\App\Console\IO\NullIO;
use CaptainHook\App\Git\DummyRepo;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

class InstallTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Install::run
     *
     * @expectedException \Exception
     */
    public function testExecuteNoConfig()
    {
        $input   = new ArrayInput(
            [
                'hook' => 'pre-commit',
                '--configuration' => 'foo',
                '--git-directory' => 'bar'
            ]
        );
        $output  = new DummyOutput();
        $install = new Install();
        $install->setIO(new NullIO());
        $install->run($input, $output);
    }

    /**
     * Tests Install::run
     *
     * @expectedException \Exception
     */
    public function testExecuteInvalidRepository()
    {
        $input   = new ArrayInput(
            [
                'hook' => 'pre-commit',
                '--configuration' => HMU_PATH_FILES . '/config/valid.json',
                '--git-directory' => 'bar/.git'
            ]
        );

        $output  = new DummyOutput();
        $install = new Install();
        $install->setIO(new NullIO());
        $install->run($input, $output);
    }


    /**
     * Tests Install::run
     */
    public function testExecutePreCommit()
    {
        $repo = new DummyRepo();
        $repo->setup();

        $install = new Install();
        $output  = new DummyOutput();
        $input   = new ArrayInput(
            [
                'hook' => 'pre-commit',
                '--configuration' => HMU_PATH_FILES . '/config/valid.json',
                '--git-directory' => $repo->getGitDir()
            ]
        );

        $install->setIO(new NullIO());
        $install->run($input, $output);

        // make sure the file is installed
        $this->assertTrue(
            file_exists(
                $repo->getGitDir() . DIRECTORY_SEPARATOR . 'hooks' . DIRECTORY_SEPARATOR . 'pre-commit'
            )
        );

        $repo->cleanup();
    }
}
