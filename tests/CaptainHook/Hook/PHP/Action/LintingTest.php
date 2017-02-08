<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Hook\PHP\Action;

use SebastianFeldmann\CaptainHook\Config;
use SebastianFeldmann\CaptainHook\Console\IO\NullIO;

class LintingTest extends \PHPUnit\Framework\TestCase
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
        $resolver = $this->getIndexOperatorMock();
        $resolver->expects($this->once())->method('getStagedFilesOfType')->willReturn($files);
        $repo->expects($this->once())->method('getIndexOperator')->willReturn($resolver);


        $action   = new Config\Action('php', '\\SebastianFeldmann\\CaptainHook\\Hook\\PHP\\Action\\Linter', []);
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
        $resolver = $this->getIndexOperatorMock();
        $resolver->expects($this->once())->method('getStagedFilesOfType')->willReturn($files);
        $repo->expects($this->once())->method('getIndexOperator')->willReturn($resolver);


        $action   = new Config\Action('php', '\\SebastianFeldmann\\CaptainHook\\Hook\\PHP\\Action\\Linter', []);
        $standard = new Linting();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * @return \SebastianFeldmann\Git\Repository
     */
    private function getRepoMock()
    {
        return $this->getMockBuilder('\\SebastianFeldmann\\Git\\Repository')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return \SebastianFeldmann\Git\Operator\Index
     */
    private function getIndexOperatorMock()
    {
        return $this->getMockBuilder('\\SebastianFeldmann\\Git\\Operator\\Index')
                    ->disableOriginalConstructor()
                    ->getMock();
    }
}
