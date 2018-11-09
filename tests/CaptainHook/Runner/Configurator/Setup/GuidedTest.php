<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Runner\Configurator\Setup;

use PHPUnit\Framework\TestCase;

class GuidedTest extends TestCase
{
    /**
     * Tests Guided::isPHPActionOptionValid
     */
    public function testPHPActionOptionValidationValid()
    {
        $this->assertEquals('foo:bar', Guided::isPHPActionOptionValid('foo:bar'));
    }

    /**
     * Tests Guided::isPHPActionOptionValid
     *
     * @expectedException \Exception
     */
    public function testPHPActionOptionValidationInvalid()
    {
        Guided::isPHPActionOptionValid('foo-bar');
    }
}
