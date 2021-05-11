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

class DoesNotContainRegexTest extends TestCase
{
    use Mockery;

    /**
     * Tests DoesNotContainRegex::execute
     *
     * @throws \Exception
     */
    public function testExecuteInvalidOption(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Missing option "regex" for DoesNotContainRegex action');

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo    = $this->createRepositoryMock();
        $action = new Config\Action(DoesNotContainRegex::class);

        $standard = new DoesNotContainRegex();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests DoesNotContainRegex::execute
     *
     * @throws \Exception
     */
    public function testExecuteSuccess(): void
    {
        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(DoesNotContainRegex::class, [
            'regex' => '#some regex that does not match#'
        ]);
        $repo   = $this->createRepositoryMock();
        $repo->method('getIndexOperator')->willReturn(
            $this->createGitIndexOperator([
                CH_PATH_FILES . '/storage/regextest1.txt'
            ])
        );

        $standard = new DoesNotContainRegex();
        $standard->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
    }

    /**
     * Tests DoesNotContainRegex::execute
     *
     * @throws \Exception
     */
    public function testExecuteFailure(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('<error>Regex \'#foo#\' failed:</error> 1 matches in 1 files');

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(DoesNotContainRegex::class, [
            'regex' => '#foo#'
        ]);
        $repo   = $this->createRepositoryMock();
        $repo->method('getIndexOperator')->willReturn(
            $this->createGitIndexOperator([
                CH_PATH_FILES . '/storage/regextest1.txt',
            ])
        );

        $standard = new DoesNotContainRegex();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests DoesNotContainRegex::execute
     *
     * @throws \Exception
     */
    public function testExecuteFailureWithCount(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('<error>Regex \'#foo#\' failed:</error> 3 matches in 2 files');

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(DoesNotContainRegex::class, [
            'regex' => '#foo#'
        ]);
        $repo   = $this->createRepositoryMock();
        $repo->method('getIndexOperator')->willReturn(
            $this->createGitIndexOperator([
                CH_PATH_FILES . '/storage/regextest1.txt',
                CH_PATH_FILES . '/storage/regextest2.txt',
            ])
        );

        $standard = new DoesNotContainRegex();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests DoesNotContainRegex::execute
     *
     * @throws \Exception
     */
    public function testExecuteSuccessWithFileExtension(): void
    {
        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(DoesNotContainRegex::class, [
            'regex' => '#.#',
            'fileExtensions' => ['php']
        ]);
        $index  = $this->createGitIndexOperator([
            CH_PATH_FILES . '/storage/regextest1.txt'
        ]);
        $index->method('getStagedFilesOfType')->willReturnCallback(function ($ext) {
            if ($ext === 'txt') {
                return [
                    CH_PATH_FILES . '/storage/regextest1.txt'
                ];
            }
            return [];
        });
        $repo = $this->createRepositoryMock();
        $repo->method('getIndexOperator')->willReturn($index);

        $standard = new DoesNotContainRegex();
        $standard->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
    }

    /**
     * Tests DoesNotContainRegex::execute
     *
     * @throws \Exception
     */
    public function testExecuteFailureWithFileExtension(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('<error>Regex \'#foo#\' failed:</error> 1 matches in 1 files');

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(DoesNotContainRegex::class, [
            'regex' => '#foo#',
            'fileExtensions' => ['txt']
        ]);
        $index  = $this->createGitIndexOperator([
            CH_PATH_FILES . '/storage/regextest1.txt'
        ]);
        $index->method('getStagedFilesOfType')->willReturnCallback(function ($ext) {
            if ($ext === 'txt') {
                return [
                    CH_PATH_FILES . '/storage/regextest1.txt'
                ];
            }
            return [];
        });
        $repo   = $this->createRepositoryMock();
        $repo->method('getIndexOperator')->willReturn($index);

        $standard = new DoesNotContainRegex();
        $standard->execute($config, $io, $repo, $action);
    }
}
