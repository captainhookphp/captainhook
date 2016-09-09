<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Git\Resolver;

class Test extends \PHPUnit_Framework_TestCase
{
    /**
     * PHPUnit cwd
     *
     * @var string
     */
    private static $cwd;

    /**
     * Dummy repo with changes
     *
     * @var string
     */
    private static $repoWithChanges;

    /**
     * Dummy repo without changes
     *
     * @var string
     */
    private static $repoNoChanges;

    /**
     * Extract two dummy git repositories to test changed files.
     */
    public static function setUpBeforeClass()
    {
        self::$cwd = getcwd();
        $tmpDir    = sys_get_temp_dir();

        $zipFileWithChanges    = CH_PATH_FILES . '/git/dummy-repo-with-changes.zip';
        $zipFileWithoutChanges = CH_PATH_FILES . '/git/dummy-repo-no-changes.zip';
        self::$repoWithChanges = $tmpDir . '/' . sha1(mt_rand(0, 999));
        self::$repoNoChanges   = $tmpDir . '/' . sha1(mt_rand(0, 999));

        $zip = new \ZipArchive();
        if ($zip->open($zipFileWithChanges) === true) {
            $zip->extractTo(self::$repoWithChanges);
            $zip->close();
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipFileWithoutChanges) === true) {
            $zip->extractTo(self::$repoNoChanges);
            $zip->close();
        }
    }

    /**
     * Remove dummy repositories.
     */
    public static function tearDownAfterClass()
    {
        system('rm -rf ' . self::$repoNoChanges);
        system('rm -rf ' . self::$repoWithChanges);
    }

    /**
     * Change directory back to default cwd.
     */
    public function tearDown()
    {
        chdir(self::$cwd);
    }

    /**
     * Tests ChangedFiles::getChangedFiles
     */
    public function testChangedGetChangedFiles()
    {
        chdir(self::$repoWithChanges . '/dummy-repo-with-changes');

        $resolver = new ChangedFiles();
        $files    = $resolver->getChangedFiles();

        $this->assertTrue(is_array($files));
        $this->assertTrue(count($files) > 0);
        $this->assertEquals('Test.php', $files[0]);
    }

    /**
     * Tests ChangedFiles::hasChangedFilesOfType
     */
    public function testChangedHasChangedFilesOfType()
    {
        chdir(self::$repoWithChanges . '/dummy-repo-with-changes');

        $resolver = new ChangedFiles();
        $bool     = $resolver->hasChangedFilesOfType('php');

        $this->assertTrue($bool);
    }

    /**
     * Tests ChangedFiles::getChangedFiles
     */
    public function testUnchangedGetChangedFiles()
    {
        chdir(self::$repoNoChanges . '/dummy-repo-no-changes');

        $resolver = new ChangedFiles();
        $files    = $resolver->getChangedFiles();

        $this->assertTrue(is_array($files));
        $this->assertTrue(count($files) == 0);
    }

    /**
     * Tests ChangedFiles::hasChangedFilesOfType
     */
    public function testUnchangedHasChangedFilesOfType()
    {
        chdir(self::$repoNoChanges . '/dummy-repo-no-changes');

        $resolver = new ChangedFiles();
        $bool     = $resolver->hasChangedFilesOfType('php');

        $this->assertFalse($bool);
    }
}
