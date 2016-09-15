<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Storage\File;

class XmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Xml::read
     *
     * @expectedException \Exception
     */
    public function testRead()
    {
        $path = realpath(CH_PATH_FILES . '/storage/invalid-xml.txt');
        $file  = new Xml($path);
        $file->read();
    }
}
