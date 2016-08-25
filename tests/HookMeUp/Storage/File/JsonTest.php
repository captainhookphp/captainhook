<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Storage\File;

class JsonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Json::read
     */
    public function testRead()
    {
        $path = realpath(__DIR__ . '/../../../files/storage/test.json');
        $json = new Json($path);
        $data = $json->read();

        $this->assertEquals('bar', $data['foo']);
    }

    /**
     * Test Json::write
     */
    public function testWrite()
    {
        $path = tempnam(sys_get_temp_dir(), 'json');
        $json = new Json($path);
        $data = ['foo' => 'bar'];
        $json->write($data);

        $json = file_get_contents($path);
        $load = json_decode($json, true);

        unlink($path);
        $this->assertEquals('bar', $load['foo']);
    }
}
