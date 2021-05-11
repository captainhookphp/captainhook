<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner\Action\Cli\Command;

use CaptainHook\App\Config\Mockery as ConfigMockery;
use CaptainHook\App\Mockery as AppMockery;
use PHPUnit\Framework\TestCase;

class FormatterTest extends TestCase
{
    use AppMockery;
    use ConfigMockery;

    /**
     * Tests Formatter::format
     */
    public function testFormatArgumentPlaceholders(): void
    {
        $args   = ['foo' => 'bar'];
        $config = $this->createConfigMock();
        $repo   = $this->createRepositoryMock();

        $formatter = new Formatter($config, $repo, $args);
        $command   = $formatter->format('cmd argument {$FOO}');

        $this->assertEquals('cmd argument bar', $command);
    }

    /**
     * Tests Formatter::format
     */
    public function testFormatInvalidPlaceholderReplacedWithEmptyString(): void
    {
        $args   = [];
        $config = $this->createConfigMock();
        $repo   = $this->createRepositoryMock();

        $formatter = new Formatter($config, $repo, $args);
        $command   = $formatter->format('cmd argument {$FOO}');

        $this->assertEquals('cmd argument ', $command);
    }

    /**
     * Tests Formatter::format
     */
    public function testCachedPlaceholder(): void
    {
        $args   = [];
        $config = $this->createConfigMock();
        $repo   = $this->createRepositoryMock();
        $index  = $this->createGitIndexOperator(['file1.php', 'file2.php']);
        $repo->expects($this->once())->method('getIndexOperator')->willReturn($index);

        $formatter = new Formatter($config, $repo, $args);
        $command1  = $formatter->format('cmd1 argument {$STAGED_FILES}');
        $command2  = $formatter->format('cmd2 argument {$STAGED_FILES}');

        $this->assertEquals('cmd1 argument file1.php file2.php', $command1);
        $this->assertEquals('cmd2 argument file1.php file2.php', $command2);
    }


    /**
     * Tests Formatter::format
     */
    public function testComplexPlaceholder(): void
    {
        $args   = [];
        $config = $this->createConfigMock();
        $repo   = $this->createRepositoryMock();
        $index  = $this->createGitIndexOperator(['file1.php', 'file2.php', 'README.md']);
        $repo->expects($this->exactly(2))->method('getIndexOperator')->willReturn($index);
        $index->expects($this->exactly(2))->method('getStagedFilesOfType')->willReturn(['file1.php', 'file2.php']);

        $formatter = new Formatter($config, $repo, $args);
        $command1  = $formatter->format('cmd1 argument {$STAGED_FILES|of-type:php|separated-by:,}');
        $command2  = $formatter->format('cmd2 argument {$STAGED_FILES|of-type:php}');

        $this->assertEquals('cmd1 argument file1.php,file2.php', $command1);
        $this->assertEquals('cmd2 argument file1.php file2.php', $command2);
    }
}
