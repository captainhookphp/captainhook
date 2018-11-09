<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Hook\Message\Rule;

/**
 * Class UseImperativeMood
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class UseImperativeMood extends Blacklist
{
    /**
     * Constructor.
     */
    public function __construct()
    {
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
}
