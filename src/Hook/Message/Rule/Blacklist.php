<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook\Message\Rule;

use function array_map;
use const PHP_EOL;
use SebastianFeldmann\Git\CommitMessage;
use function strpos;
use function strtolower;

/**
 * Class UseImperativeMood
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
class Blacklist extends Base
{
    /**
     * Case sensitivity
     *
     * @var bool
     */
    protected $isCaseSensitive;

    /**
     * Blacklisted words
     *
     * @var array
     */
    protected $blacklist = [
        'subject' => [],
        'body'    => [],
    ];

    /**
     * Constructor
     *
     * @param bool $caseSensitive
     */
    public function __construct(bool $caseSensitive = false)
    {
        $this->isCaseSensitive = $caseSensitive;
        $this->hint            = 'Commit message should not contain blacklisted words';
    }

    /**
     * Set body blacklist
     *
     * @param  array $list
     * @return void
     */
    public function setBodyBlacklist(array $list) : void
    {
        $this->setBlacklist($list, 'body');
    }

    /**
     * Set subject blacklist
     *
     * @param  array $list
     * @return void
     */
    public function setSubjectBlacklist(array $list) : void
    {
        $this->setBlacklist($list, 'subject');
    }

    /**
     * Blacklist setter
     *
     * @param  array  $list
     * @param  string $type
     * @return void
     */
    protected function setBlacklist(array $list, string $type) : void
    {
        $this->blacklist[$type] = $list;
    }

    /**
     * Check if the message contains blacklisted words
     *
     * @param  \SebastianFeldmann\Git\CommitMessage $msg
     * @return bool
     */
    public function pass(CommitMessage $msg) : bool
    {
        return $this->isSubjectValid($msg) && $this->isBodyValid($msg);
    }

    /**
     * Check commit message subject for blacklisted words
     *
     * @param \SebastianFeldmann\Git\CommitMessage $msg
     * @return bool
     */
    protected function isSubjectValid(CommitMessage $msg) : bool
    {
        return !$this->containsBlacklistedWord($this->blacklist['subject'], $msg->getSubject());
    }

    /**
     * Check commit message body for blacklisted words
     *
     * @param \SebastianFeldmann\Git\CommitMessage $msg
     * @return bool
     */
    protected function isBodyValid(CommitMessage $msg) : bool
    {
        return !$this->containsBlacklistedWord($this->blacklist['body'], $msg->getBody());
    }

    /**
     * Contains blacklisted word
     *
     * @param  array  $list
     * @param  string $content
     * @return bool
     */
    protected function containsBlacklistedWord(array $list, string $content) : bool
    {
        return $this->compareContentAgainstWordListUsingCallback($content, $list, function ($content, $term) : bool {
            return strpos($content, $term) !== false;
        });
    }

    /**
     * Contains blacklisted word
     *
     * Calable has to accept two parameters. The first is the string to check, the second is the string that is not
     * supposed to be contained in the given string
     *
     * @param  array  $list
     * @param  string $content
     * @param callable $callable
     * @return bool
     */
    protected function compareContentAgainstWordListUsingCallback(string $content, array $list, Callable $callable) : bool
    {
        if (!$this->isCaseSensitive) {
            $content = strtolower($content);
            $list    = array_map('strtolower', $list);
        }
        foreach ($list as $term) {
            if ($callable($content, $term)) {
                $this->hint .= PHP_EOL . 'Invalid use of \'' . $term . '\'';
                return true;
            }
        }
        return false;
    }
}
