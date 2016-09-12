<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Hook\PHP\Action;

use sebastianfeldmann\CaptainHook\Config;
use sebastianfeldmann\CaptainHook\Console\IO\NullIO;

class LintingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Linter::execute
     */
    public function testExecuteValidPHP()
    {
        $io       = new NullIO();
        $config   = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo     = $this->getRepoMock();
        $files    = [CH_PATH_FILES . '/php/valid.txt'];
        $resolver = $this->getChangedFilesResolverMock();
        $resolver->expects($this->once())->method('getChangedFilesOfType')->willReturn($files);
        $repo->expects($this->once())->method('getChangedFilesResolver')->willReturn($resolver);


        $action   = new Config\Action('php', '\\sebastianfeldmann\\CaptainHook\\Hook\\PHP\\Action\\Linter', []);
        $standard = new Linting();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests Linter::execute
     *
     * @expectedException \Exception
     */
    public function testExecuteInvalidPHP()
    {
        $io       = new NullIO();
        $config   = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo     = $this->getRepoMock();
        $files    = [CH_PATH_FILES . '/php/invalid.txt'];
        $resolver = $this->getChangedFilesResolverMock();
        $resolver->expects($this->once())->method('getChangedFilesOfType')->willReturn($files);
        $repo->expects($this->once())->method('getChangedFilesResolver')->willReturn($resolver);


        $action   = new Config\Action('php', '\\sebastianfeldmann\\CaptainHook\\Hook\\PHP\\Action\\Linter', []);
        $standard = new Linting();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * @return \sebastianfeldmann\CaptainHook\Git\Repository
     */
    private function getRepoMock()
    {
        return $this->getMockBuilder('\\sebastianfeldmann\\CaptainHook\\Git\\Repository')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \sebastianfeldmann\CaptainHook\Git\Resolver\ChangedFiles
     */
    private function getChangedFilesResolverMock()
    {
        return $this->getMockBuilder('\\sebastianfeldmann\\CaptainHook\\Git\\Resolver\\ChangedFiles')
                    ->disableOriginalConstructor()
                    ->getMock();
    }
}
