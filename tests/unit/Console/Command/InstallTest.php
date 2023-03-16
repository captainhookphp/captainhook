<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Console\Command;

use CaptainHook\App\Console\IO\NullIO;
use CaptainHook\App\Console\Runtime\Resolver;
use CaptainHook\App\Git\DummyRepo;
use CaptainHook\App\Hook\Template;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class InstallTest extends TestCase
{
    /**
     * Tests Install::run
     *
     * @throws \Exception
     */
    public function testFailMissingConfig(): void
    {
        $this->expectException(Exception::class);

        $output = new NullOutput();
        $input  = new ArrayInput(
            [
                'hook'            => 'pre-commit',
                '--configuration' => 'foo',
                '--git-directory' => 'bar'
            ]
        );

        $install = new Install(new Resolver(CH_PATH_FILES . '/bin/captainhook'));
        $install->run($input, $output);
    }

    /**
     * Tests Install::run
     *
     * @throws \Exception
     */
    public function testFailInvalidRepository(): void
    {
        $this->expectException(Exception::class);

        $output = new NullOutput();
        $input  = new ArrayInput(
            [
                'hook'            => 'pre-commit',
                '--configuration' => CH_PATH_FILES . '/config/valid.json',
                '--git-directory' => 'bar/.git'
            ]
        );

        $install = new Install(new Resolver(CH_PATH_FILES . '/bin/captainhook'));
        $install->setIO(new NullIO());
        $install->run($input, $output);
    }

    /**
     * Tests Install::run
     *
     * @throws \Exception
     */
    public function testFailMissingRunExecOption(): void
    {
        $this->expectException(Exception::class);

        $repo   = new DummyRepo();
        $output = new NullOutput();
        $input  = new ArrayInput(
            [
                'hook'            => 'pre-commit',
                '--configuration' => CH_PATH_FILES . '/template/captainhook.json',
                '--git-directory' => $repo->getGitDir(),
                '--run-mode'      => Template::DOCKER
            ]
        );

        $install = new Install(new Resolver(CH_PATH_FILES . '/bin/captainhook'));
        $install->run($input, $output);
    }


    /**
     * Tests Install::run
     *
     * @throws \Exception
     */
    public function testInstallPreCommitHook(): void
    {
        $repo   = new DummyRepo();
        $output = new NullOutput();
        $input  = new ArrayInput(
            [
                'hook'            => 'pre-commit',
                '--configuration' => CH_PATH_FILES . '/template/captainhook.json',
                '--git-directory' => $repo->getGitDir()
            ]
        );

        $install = new Install(new Resolver(CH_PATH_FILES . '/bin/captainhook'));
        $install->run($input, $output);

        $this->assertTrue($repo->hookExists('pre-commit'));
    }

    /**
     * Tests Install::run
     *
     * @throws \Exception
     */
    public function testInstallMultipleHooks(): void
    {
        $repo   = new DummyRepo();
        $output = new NullOutput();
        $input  = new ArrayInput(
            [
                'hook'            => 'pre-commit,pre-push,post-checkout',
                '--configuration' => CH_PATH_FILES . '/template/captainhook.json',
                '--git-directory' => $repo->getGitDir(),
            ]
        );

        $install = new Install(new Resolver(CH_PATH_FILES . '/bin/captainhook'));
        $install->run($input, $output);

        $this->assertTrue($repo->hookExists('pre-commit'));
        $this->assertTrue($repo->hookExists('pre-push'));
        $this->assertTrue($repo->hookExists('post-checkout'));
    }

    /**
     * Tests Install::run
     *
     * @throws \Exception
     */
    public function testInstallMultipleHooksWithSpacesAfterAndBetweenSeparator(): void
    {
        $repo   = new DummyRepo();
        $output = new NullOutput();
        $input  = new ArrayInput(
            [
                'hook'            => ' pre-commit , pre-push , post-checkout, post-commit',
                '--configuration' => CH_PATH_FILES . '/template/captainhook.json',
                '--git-directory' => $repo->getGitDir(),
            ]
        );

        $install = new Install(new Resolver(CH_PATH_FILES . '/bin/captainhook'));
        $install->run($input, $output);

        $this->assertTrue($repo->hookExists('pre-commit'));
        $this->assertTrue($repo->hookExists('pre-push'));
        $this->assertTrue($repo->hookExists('post-checkout'));
    }

    /**
     * Tests Install::run
     *
     * @throws \Exception
     */
    public function testInstallMultipleHooksWithOneWrong(): void
    {
        $this->expectException(\CaptainHook\App\Exception\InvalidHookName::class);
        $repo   = new DummyRepo();
        $output = new NullOutput();
        $input  = new ArrayInput(
            [
                'hook'            => 'pre-commit,pre-push,post-checkout,something-wrong',
                '--configuration' => CH_PATH_FILES . '/template/captainhook.json',
                '--git-directory' => $repo->getGitDir(),
            ]
        );

        $install = new Install(new Resolver(CH_PATH_FILES . '/bin/captainhook'));
        $install->run($input, $output);
    }


    /**
     * Tests Install::run
     *
     * @throws \Exception
     */
    public function testInstallMultipleHooksWithMultipleWrong(): void
    {
        $this->expectException(\CaptainHook\App\Exception\InvalidHookName::class);
        $repo   = new DummyRepo();
        $output = new NullOutput();
        $input  = new ArrayInput(
            [
                'hook'            => 'pre-commit,pre-push,post-checkout,something-wrong1,something-wrong2',
                '--configuration' => CH_PATH_FILES . '/template/captainhook.json',
                '--git-directory' => $repo->getGitDir(),
            ]
        );

        $install = new Install(new Resolver(CH_PATH_FILES . '/bin/captainhook'));
        $install->run($input, $output);
    }
}
