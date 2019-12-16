<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\PHP\CoverageResolver;

use Exception;
use PHPUnit\Framework\TestCase;

class CloverXMLTest extends TestCase
{
    /**
     * Tests CloverXML::getCoverage
     */
    public function testValid(): void
    {
        $resolver = new CloverXML(CH_PATH_FILES . '/coverage/valid.xml');
        $coverage = $resolver->getCoverage();

        $this->assertEquals(95.0, $coverage);
    }

    /**
     * Tests CloverXML::__construct
     */
    public function testFileNotFound(): void
    {
        $this->expectException(Exception::class);

        $resolver = new CloverXML('foo.xml');
        $this->assertNull($resolver);
    }

    /**
     * Tests CloverXML::__construct
     */
    public function testInvalidXML(): void
    {
        $this->expectException(Exception::class);

        $resolver = new CloverXML(CH_PATH_FILES . '/coverage/no-metrics.xml');
        $this->assertNull($resolver);
    }

    /**
     * Tests CloverXML::__construct
     */
    public function testInvalidMetrics(): void
    {
        $this->expectException(Exception::class);

        $resolver = new CloverXML(CH_PATH_FILES . '/coverage/invalid-metrics.xml');
        $resolver->getCoverage();
    }
}
