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
use CaptainHook\App\Hook\Template;
use Exception;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;
use PHPUnit\Framework\TestCase;

class InstallTest extends TestCase
{
    /**
     * Tests Install::run
     */
    public function testExecuteNoConfig(): void
    {
        $this->expectException(Exception::class);

        $install = new Install();
        $output  = new DummyOutput();
        $input   = new ArrayInput(
            [
                'hook'            => 'pre-commit',
                '--configuration' => 'foo',
                '--git-directory' => 'bar'
            ]
        );
        $install->setIO(new NullIO());
        $install->run($input, $output);
    }

    /**
     * Tests Install::run
     */
    public function testExecuteInvalidRepository(): void
    {
        $this->expectException(Exception::class);

        $install = new Install();
        $output  = new DummyOutput();
        $input   = new ArrayInput(
            [
                'hook'            => 'pre-commit',
                '--configuration' => CH_PATH_FILES . '/config/valid.json',
                '--git-directory' => 'bar/.git'
            ]
        );

        $install->setIO(new NullIO());
        $install->run($input, $output);
    }


    /**
     * Tests Install::run
     */
    public function testExecuteMissingCommandName(): void
    {
        $repo = new DummyRepo();
        $repo->setup();

        try {
            $install = new Install();
            $output  = new DummyOutput();
            $input   = new ArrayInput(
                [
                    'hook'            => 'pre-commit',
                    '--configuration' => CH_PATH_FILES . '/config/valid.json',
                    '--git-directory' => $repo->getGitDir(),
                    '--run-mode'      => Template::DOCKER
                ]
            );

            $install->setIO(new NullIO());
            $install->run($input, $output);
        } catch (Exception $e) {
            $this->assertEquals('Option "command" missing for run-mode docker.', $e->getMessage());
            $this->assertTrue(true, 'Exception should be thrown');
        } finally {
            $repo->cleanup();
        }
    }


    /**
     * Tests Install::run
     */
    public function testExecutePreCommit(): void
    {
        $repo = new DummyRepo();
        $repo->setup();

        $install = new Install();
        $output  = new DummyOutput();
        $input   = new ArrayInput(
            [
                'hook'            => 'pre-commit',
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
