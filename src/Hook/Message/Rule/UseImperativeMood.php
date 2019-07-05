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

use function strpos;

/**
 * Class UseImperativeMood
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
class UseImperativeMood extends Blacklist
{
    private $checkOnlyBeginning;
    /**
     * Constructor
     */
    public function __construct($checkOnlyBeginning = false)
    {
        $this->checkOnlyBeginning = $checkOnlyBeginning;
        parent::__construct(false);
        $this->hint = 'Subject should be written in imperative mood';
        $this->setSubjectBlacklist(
            [
                'added',
                'changed',
                'created',
                'fixed',
                'removed',
                'updated',
                'uploaded',
            ]
        );
    }

    protected function containsBlacklistedWord(array $list, string $content) : bool
    {
        if ($this->checkOnlyBeginning === true) {
            return $this->compareContentAgainstWordListUsingCallback($content, $list, function ($content, $term): bool {
                return strpos($content, $term) === 0;
            });
        }

        return parent::containsBlacklistedWord($list, $content);
    }


}
