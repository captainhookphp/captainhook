<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner\Action\Cli\Command\Placeholder;

use CaptainHook\App\Mockery as AppMockery;
use PHPUnit\Framework\TestCase;

class StagedFilesTest extends TestCase
{
    use AppMockery;

    /**
     * Tests StagedFiles::replacement
     */
    public function testCustomSeparator(): void
    {
        $repo  = $this->createRepositoryMock();
        $index = $this->createGitIndexOperator(['file1.php', 'file2.php', 'README.md']);
        $repo->expects($this->once())->method('getIndexOperator')->willReturn($index);

        $placeholder = new StagedFiles($repo);
        $command     = $placeholder->replacement(['separated-by' => ', ']);

        $this->assertEquals('file1.php, file2.php, README.md', $command);
    }

    /**
     * Tests StagedFiles::replacement
     */
    public function testOfType(): void
    {
        $repo  = $this->createRepositoryMock();
        $index = $this->createGitIndexOperator(['file1.php', 'file2.php', 'README.md']);
        $repo->expects($this->once())->method('getIndexOperator')->willReturn($index);
        $index->expects($this->once())->method('getStagedFilesOfType')->willReturn(['file1.php', 'file2.php']);

        $placeholder = new StagedFiles($repo);
        $command     = $placeholder->replacement(['of-type' => 'php']);

        $this->assertEquals('file1.php file2.php', $command);
    }
}
