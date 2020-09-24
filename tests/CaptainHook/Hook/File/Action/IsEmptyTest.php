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

class IsEmptyTest extends TestCase
{
    use Mockery;

    /**
     * Tests IsEmpty::execute
     *
     * @throws \Exception
     */
    public function testExecuteInvalidOption(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Missing option "files" for IsEmpty action');

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo    = $this->createRepositoryMock();
        $action = new Config\Action(Regex::class);

        $standard = new IsEmpty();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests RegexCheck::execute
     *
     * @throws \Exception
     */
    public function testExecuteSuccess(): void
    {
        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(IsEmpty::class, ['files' => [
            CH_PATH_FILES . '/doesNotExist.txt',
            CH_PATH_FILES . '/storage/empty.log',
        ]]);
        $repo   = $this->createRepositoryMock();

        $standard = new IsEmpty();
        $standard->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
    }

    /**
     * Tests RegexCheck::execute
     *
     * @throws \Exception
     */
    public function testExecuteFailure(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('<error>Error: 1 non-empty file(s)</error>');

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(IsEmpty::class, ['files' => [
            CH_PATH_FILES . '/doesNotExist.txt', // pass
            CH_PATH_FILES . '/storage/empty.log', // pass
            CH_PATH_FILES . '/storage/test.json', // fail
        ]]);
        $repo   = $this->createRepositoryMock();

        $standard = new IsEmpty();
        $standard->execute($config, $io, $repo, $action);
    }
}
