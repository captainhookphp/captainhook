<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook\PHP\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO\NullIO;
use CaptainHook\App\Hook\PHP\Action\Linter;
use CaptainHook\App\Mockery;
use SebastianFeldmann\Git\Operator\Index;
use SebastianFeldmann\Git\Repository;
use PHPUnit\Framework\TestCase;

class LintingTest extends TestCase
{
    use Mockery;

    /**
     * Tests Linter::execute
     */
    public function testExecuteValidPHP(): void
    {
        $io       = new NullIO();
        $config   = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo     = $this->createRepositoryMock();
        $files    = [CH_PATH_FILES . '/php/valid.txt'];
        $resolver = $this->createGitIndexOperator();
        $resolver->expects($this->once())->method('getStagedFilesOfType')->willReturn($files);
        $repo->expects($this->once())->method('getIndexOperator')->willReturn($resolver);


        $action   = new Config\Action(Linter::class, []);
        $standard = new Linting();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests Linter::execute
     */
    public function testExecuteInvalidPHP(): void
    {
        $this->expectException(\Exception::class);

        $io       = new NullIO();
        $config   = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo     = $this->createRepositoryMock();
        $files    = [CH_PATH_FILES . '/php/invalid.txt'];
        $resolver = $this->createGitIndexOperator();
        $resolver->expects($this->once())->method('getStagedFilesOfType')->willReturn($files);
        $repo->expects($this->once())->method('getIndexOperator')->willReturn($resolver);


        $action   = new Config\Action(Linter::class, []);
        $standard = new Linting();
        $standard->execute($config, $io, $repo, $action);
    }
}
