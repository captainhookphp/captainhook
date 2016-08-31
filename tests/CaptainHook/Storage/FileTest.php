<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Storage;

class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests File::getPath
     */
    public function testGetPath()
    {
        $file = new File(__FILE__);
        $this->assertEquals(__FILE__, $file->getPath());
    }

    /**
     * Tests File::read
     */
    public function testRead()
    {
        $file    = new File(__FILE__);
        $content = $file->read();

        $this->assertTrue((bool)strstr($content, '<?php'));
    }

    /**
     * Tests File::read
     *
     * @expectedException \Exception
     */
    public function testReadFail()
    {
        $file    = new File(__FILE__ . '.absent');
        $content = $file->read();

        $this->assertTrue(false);
    }

    /**
     * Tests File::write
     */
    public function testWrite()
    {
        $tmpDir = sys_get_temp_dir();
        $path   = tempnam($tmpDir, 'foo');
        $file   = new File($path);
        $file->write('foo');

        $this->assertEquals('foo', file_get_contents($path));
        $this->assertTrue(unlink($path));
    }

    /**
     * Tests File::write
     *
     * @expectedException \Exception
     */
    public function testWriteFailNoDir()
    {
        $path   = __FILE__ . DIRECTORY_SEPARATOR . 'foo.txt';
        $file   = new File($path);
        $file->write('foo');

        $this->assertTrue(false);
    }

    /**
     * Tests File::write
     *
     * @expectedException \Exception
     */
    public function testNoWritePermission()
    {
        $path = tempnam(sys_get_temp_dir(), 'noPermission');
        chmod($path, 0000);

        $file = new File($path);
        $file->write('test');
    }

    /**
     * Tests File::write
     *
     * @expectedException \Exception
     */
    public function testCantCreateDirectory()
    {
        $baseDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('basedir');
        mkdir($baseDir, 0000);

        $path = $baseDir . '/foo/bar.txt';
        $file = new File($path);
        $file->write('test');
    }
}
