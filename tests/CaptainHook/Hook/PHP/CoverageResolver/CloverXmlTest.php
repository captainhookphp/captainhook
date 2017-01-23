<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Hook\PHP\CoverageResolver;

class CloverXMLTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests CloverXML::getCoverage
     */
    public function testValid()
    {
        $resolver = new CloverXML(CH_PATH_FILES . '/coverage/valid.xml');
        $coverage = $resolver->getCoverage();

        $this->assertEquals(95.0, $coverage);
    }

    /**
     * Tests CloverXML::__construct
     *
     * @expectedException \Exception
     */
    public function testFileNotFound()
    {
        $resolver = new CloverXML('foo.xml');
    }

    /**
     * Tests CloverXML::__construct
     *
     * @expectedException \Exception
     */
    public function testInvalidXML() {
        $resolver = new CloverXML(CH_PATH_FILES . '/coverage/no-metrics.xml');
    }

    /**
     * Tests CloverXML::__construct
     *
     * @expectedException \Exception
     */
    public function testInvalidMetrics() {
        $resolver = new CloverXML(CH_PATH_FILES . '/coverage/invalid-metrics.xml');
        $resolver->getCoverage();
    }
}
