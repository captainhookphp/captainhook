<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Git;

class DummyRepo
{
    private $path;

    private $gitDir;

    public function __construct($name = null)
    {
        $name         = empty($name) ? md5(mt_rand(0, 9999)) : $name;
        $this->path   = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $name;
        $this->gitDir = $this->path . DIRECTORY_SEPARATOR . '.git';
    }

    public function setup()
    {
        mkdir($this->gitDir . DIRECTORY_SEPARATOR . 'hooks', 0777, true);
    }

    public function touchHook($name, $content = '# dummy hook')
    {
        file_put_contents($this->gitDir . DIRECTORY_SEPARATOR . 'hooks' . DIRECTORY_SEPARATOR . $name, $content);
    }

    public function merge()
    {
        file_put_contents($this->gitDir . DIRECTORY_SEPARATOR . 'MERGE_MSG', '# merge file');
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getGitDir()
    {
        return $this->path . DIRECTORY_SEPARATOR . '.git';
    }

    public function cleanup()
    {
        system('rm -rf ' . $this->path);
    }
}
