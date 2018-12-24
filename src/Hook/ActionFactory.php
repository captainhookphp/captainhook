<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook;

use CaptainHook\App\Config;

/**
 * Interface ActionFactory
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captaionhookphp/captainhook
 * @since   Interface available since Release 3.1.1
 */
interface ActionFactory
{
    /**
     * Retrieve the action.
     *
     * @param  \CaptainHook\App\Config\Options  $option
     *
     * @return Action
     * @throws \Exception
     */
    public function getAction(Config\Options $option) : Action;
}
