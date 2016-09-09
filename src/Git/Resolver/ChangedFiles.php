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

class ChangedFiles
{
    /**
     * List of changed files.
     *
     * @var array
     */
    private $files;

    /**
     * Changed files by file type
     *
     * @var array[]
     */
    private $types = [];

    /**
     * Files sorted by suffix yet
     *
     * @var bool
     */
    private $typesResolved = false;

    /**
     * Get the lst of files that changed.
     *
     * @return array
     */
    public function getChangedFiles()
    {
        if (null === $this->files) {
            $this->resolveFiles();
        }
        return $this->files;
    }

    /**
     * Where there files changed of a given type.
     *
     * @param  string $suffix
     * @return bool
     */
    public function hasChangedFilesOfType($suffix)
    {
        return count($this->getChangedFilesOfType($suffix)) > 0;
    }

    /**
     * Return list of changed files of a given type.
     *
     * @param  string $suffix
     * @return array
     */
    public function getChangedFilesOfType($suffix)
    {
        if (!$this->typesResolved) {
            $this->resolveFileTypes();
        }
        return isset($this->types[$suffix]) ? $this->types[$suffix] : [];
    }

    /**
     * Resolve the list of files that changed.
     */
    private function resolveFiles()
    {
        $this->files = [];
        $headSHA1    = null;
        $return      = null;
        // get current commit hash
        exec('git rev-parse --verify HEAD 2> /dev/null', $headSHA1, $return);

        if (0 === $return) {
            // get changed files
            exec("git diff-index --cached --name-status HEAD | egrep '^(A|M)' | awk '{print $2;}'", $this->files);
        }
    }

    /**
     * Sort files by file suffix.
     */
    private function resolveFileTypes()
    {
        foreach ($this->getChangedFiles() as $file) {
            $ext                 = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $this->types[$ext][] = $file;
        }
        $this->typesResolved = true;
    }
}
