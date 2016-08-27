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

/**
 * Class Repository
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
class Repository
{
    /**
     * Path to git repository root
     *
     * @var string
     */
    private $root;

    /**
     * Path to .git directory
     *
     * @var string
     */
    private $dotGitDir;

    /**
     * Commit message data
     *
     * @var \HookMeUp\Git\CommitMessage
     */
    private $commitMsg;

    /**
     * Repository constructor.
     *
     * @param string $root
     */
    public function __construct($root = null)
    {
        $this->root      = null === $root ? getcwd() : $root;
        $this->dotGitDir = $this->root . DIRECTORY_SEPARATOR . '.git';
        // check if it's a valid git repository
        if (!is_dir($this->dotGitDir)) {
            throw new \RuntimeException('Invalid git repository path: ' . $this->root);
        }
    }

    /**
     * Returns the path to the hooks directory.
     *
     * @return string
     */
    public function getHooksDir()
    {
        return $this->dotGitDir . DIRECTORY_SEPARATOR . 'hooks';
    }

    /**
     * Check for a hook file.
     *
     * @param  string $hook
     * @return bool
     */
    public function hookExists($hook)
    {
        return file_exists($this->getHooksDir() . DIRECTORY_SEPARATOR . $hook);
    }

    /**
     * CommitMessage setter.
     *
     * @param \HookMeUp\Git\CommitMessage $commitMsg
     */
    public function setCommitMsg(CommitMessage $commitMsg)
    {
        $this->commitMsg = $commitMsg;
    }

    /**
     * CommitMessage getter.
     *
     * @return \HookMeUp\Git\CommitMessage
     */
    public function getCommitMsg()
    {
        if (null === $this->commitMsg) {
            throw new \RuntimeException('No commit message available');
        }
        return $this->commitMsg;
    }

    /**
     * Is there a merge in progress.
     *
     * @return bool
     */
    public function isMerging()
    {
        foreach (['MERGE_MSG', 'MERGE_HEAD', 'MERGE_MODE'] as $fileName) {
            if (file_exists($this->dotGitDir . DIRECTORY_SEPARATOR . $fileName)) {
                return true;
            }
        }
        return false;
    }
}
