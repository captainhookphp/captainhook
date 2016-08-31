<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\App\Git;

/**
 * Class CommitMessage
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
class CommitMessage
{
    /**
     * Commit Message content
     *
     * @var string
     */
    private $content;

    /**
     * Content split lines
     *
     * @var string[]
     */
    private $lines;

    /**
     * Amount of lines
     *
     * @var int
     */
    private $lineCount;

    /**
     * CommitMessage constructor.
     *
     * @param string $content
     */
    public function __construct($content)
    {
        $this->content   = $content;
        $this->lines     = empty($content) ? [] : preg_split("/\\r\\n|\\r|\\n/", $content);
        $this->lineCount = count($this->lines);
    }

    /**
     * Is message empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->content);
    }

    /**
     * Get complete commit message content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Return all lines.
     *
     * @return array
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * Return line count.
     *
     * @return int
     */
    public function getLineCount()
    {
        return $this->lineCount;
    }

    /**
     * Get a specific line.
     *
     * @param  int $index
     * @return string
     */
    public function getLine($index)
    {
        return isset($this->lines[$index]) ? $this->lines[$index] : '';
    }

    /**
     * Return first line.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->lines[0];
    }

    /**
     * Return content from line nr. 3 to the last line.
     *
     * @return string
     */
    public function getBody()
    {
        return implode(PHP_EOL, $this->getBodyLines());
    }

    /**
     * Return lines from line nr. 3 to the last line.
     *
     * @return array
     */
    public function getBodyLines()
    {
        return $this->lineCount < 3 ? [] : array_slice($this->lines, 2);
    }

    /**
     * Create CommitMessage from file.
     *
     * @param  string $path
     * @return \HookMeUp\App\Git\CommitMessage
     */
    public static function createFromFile($path)
    {
        if (!file_exists($path)) {
            throw new \RuntimeException('Commit message file not found');
        }
        return new CommitMessage(file_get_contents($path));
    }
}
