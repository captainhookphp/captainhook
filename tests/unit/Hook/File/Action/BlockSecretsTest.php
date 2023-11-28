<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\File\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO\NullIO;
use CaptainHook\App\Hook\Debug;
use CaptainHook\App\Hook\File\Regex\Aws;
use CaptainHook\App\Hook\File\Regex\GitHub;
use CaptainHook\App\Hook\File\Regex\Google;
use CaptainHook\App\Hook\File\Regex\Password;
use CaptainHook\App\Hook\File\Regex\Stripe;
use CaptainHook\App\Mockery;
use Exception;
use PHPUnit\Framework\TestCase;

class BlockSecretsTest extends TestCase
{
    use Mockery;

    /**
     * Tests BlockSecrets::execute
     *
     * @throws \Exception
     */
    public function testExecuteSuccess(): void
    {
        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(BlockSecrets::class, []);
        $repo   = $this->createRepositoryMock();
        $repo->method('getIndexOperator')->willReturn(
            $this->createGitIndexOperator([
                CH_PATH_FILES . '/storage/secrets-ok.txt'
            ])
        );

        $standard = new BlockSecrets();
        $standard->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
    }

    /**
     * Tests BlockSecrets::execute
     *
     * @throws \Exception
     */
    public function testExecuteFailure(): void
    {
        $this->expectException(Exception::class);

        $options = [
            'providers' => [
                Aws::class,
                Password::class,
                Google::class,
                GitHub::class,
                Stripe::class
            ]
        ];

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(BlockSecrets::class, $options);
        $repo   = $this->createRepositoryMock();
        $repo->method('getIndexOperator')->willReturn(
            $this->createGitIndexOperator([
                CH_PATH_FILES . '/storage/secrets-fail.txt',
            ])
        );

        $standard = new BlockSecrets();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests BlockSecrets::execute
     *
     * @throws \Exception
     */
    public function testExecuteProviderNotFound(): void
    {
        $this->expectException(Exception::class);

        $options = [
            'providers' => [
                'Fooooooooooooo'
            ]
        ];

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(BlockSecrets::class, $options);
        $repo   = $this->createRepositoryMock();
        $repo->method('getIndexOperator')->willReturn(
            $this->createGitIndexOperator([
                CH_PATH_FILES . '/storage/secrets-fail.txt',
            ])
        );

        $standard = new BlockSecrets();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests BlockSecrets::execute
     *
     * @throws \Exception
     */
    public function testExecuteInvalidProvider(): void
    {
        $this->expectException(Exception::class);

        $options = [
            'providers' => [
                Debug::class
            ]
        ];

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(BlockSecrets::class, $options);
        $repo   = $this->createRepositoryMock();
        $repo->method('getIndexOperator')->willReturn(
            $this->createGitIndexOperator([
                CH_PATH_FILES . '/storage/secrets-fail.txt',
            ])
        );

        $standard = new BlockSecrets();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests BlockSecrets::execute
     */
    public function testExecuteAllow(): void
    {
        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(BlockSecrets::class, [
            'blocked'   => ['#f[a-z]+#'],
            'allowed'   => ['#foo#']
        ]);
        $repo = $this->createRepositoryMock();
        $repo->expects($this->atLeast(1))->method('getIndexOperator')->willReturn(
            $this->createGitIndexOperator([
                CH_PATH_FILES . '/storage/secrets-ok.txt',
            ])
        );

        $standard = new BlockSecrets();
        $standard->execute($config, $io, $repo, $action);
    }
}
