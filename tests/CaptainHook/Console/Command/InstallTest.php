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
use PHPUnit\Framework\TestCase;

class InstallTest extends TestCase
{
    /**
     * Tests Install::run
     */
    public function testExecuteNoConfig()
    {
        $this->expectException(\Exception::class);

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
     */
    public function testExecuteInvalidRepository()
    {
        $this->expectException(\Exception::class);

        $input   = new ArrayInput(
            [
                'hook' => 'pre-commit',
                '--configuration' => CH_PATH_FILES . '/config/valid.json',
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
                '--configuration' => CH_PATH_FILES . '/config/valid.json',
                '--git-directory' => $repo->getGitDir()
            ]
        );

        $install->setIO(new NullIO());
        $install->run($input, $output);

        // make sure the file is installed
        $this->assertFileExists($repo->getGitDir() . DIRECTORY_SEPARATOR . 'hooks' . DIRECTORY_SEPARATOR . 'pre-commit');

        $repo->cleanup();
    }
}
