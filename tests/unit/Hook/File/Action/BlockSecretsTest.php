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

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(BlockSecrets::class, []);
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
            'blockDefaults'  => false,
            'blocked' => ['#f[a-z]+#'],
            'allowed' => ['#foo#']
        ]);
        $repo   = $this->createRepositoryMock();
        $repo->expects($this->atLeast(1))->method('getIndexOperator')->willReturn(
            $this->createGitIndexOperator([
                CH_PATH_FILES . '/storage/secrets-ok.txt',
            ])
        );

        $standard = new BlockSecrets();
        $standard->execute($config, $io, $repo, $action);
    }
}
