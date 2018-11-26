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

class TestCoverageTest extends \PHPUnit\Framework\TestCase
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
            '\\CaptainHook\\App\\Hook\\PHP\\Action\\TextCoverage',
            ['cloverXml' => CH_PATH_FILES . '/coverage/valid.xml']
        );
        $standard->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
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
            '\\CaptainHook\\App\\Hook\\PHP\\Action\\TextCoverage',
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
            '\\CaptainHook\\App\\Hook\\PHP\\Action\\TextCoverage',
            ['phpUnit' => CH_PATH_FILES . '/bin/phpunit']
        );
        $standard->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
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
}
