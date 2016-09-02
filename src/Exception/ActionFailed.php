<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Exception;

/**
 * Class ActionFailed
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class ActionFailed extends \Exception
{
    /**
     * Return a Action Failed exception.
     *
     * @param string $msg
     * @return \sebastianfeldmann\CaptainHook\Exception\ActionFailed
     */
    public static function withMessage($msg)
    {
        return new self($msg);
    }
}
