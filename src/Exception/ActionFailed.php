<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Exception;

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
     * @return \SebastianFeldmann\CaptainHook\Exception\ActionFailed
     */
    public static function withMessage($msg)
    {
        return new self($msg);
    }

    /**
     * Create a new exception based on a previous exception.
     *
     * @param  \SebastianFeldmann\CaptainHook\Exception\ActionFailed $exception
     * @return \SebastianFeldmann\CaptainHook\Exception\ActionFailed
     */
    public static function fromPrevious(ActionFailed $exception)
    {
        return new self($exception->getMessage(), $exception->getCode(), $exception);
    }
}
