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

class TestCoverageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests TestCoverage::execute
     */
    public function testCoverageViaCloverXML()
    {
        $io       = new NullIO();
        $config   = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo     = $this->getRepoMock();
        $standard = new TestCoverage();
        $action   = new Config\Action(
            'php',
            '\\sebastianfeldmann\\CaptainHook\\Hook\\PHP\\Action\\TextCoverage',
            ['cloverXml' => CH_PATH_FILES . '/coverage/valid.xml']
        );
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests TestCoverage::execute
     *
     * @expectedException \Exception
     */
    public function testCoverageLow()
    {
        $io       = new NullIO();
        $config   = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo     = $this->getRepoMock();
        $standard = new TestCoverage();
        $action   = new Config\Action(
            'php',
            '\\sebastianfeldmann\\CaptainHook\\Hook\\PHP\\Action\\TextCoverage',
            [
                'cloverXml'   => CH_PATH_FILES . '/coverage/valid.xml',
                'minCoverage' => 100
            ]
        );
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests TestCoverage::execute
     */
    public function testCoverageViaPHPUnit()
    {
        $io       = new NullIO();
        $config   = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo     = $this->getRepoMock();
        $standard = new TestCoverage();
        $action   = new Config\Action(
            'php',
            '\\sebastianfeldmann\\CaptainHook\\Hook\\PHP\\Action\\TextCoverage',
            ['phpUnit' => CH_PATH_FILES . '/bin/phpunit']
        );
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
}
