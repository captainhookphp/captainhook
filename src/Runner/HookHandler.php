<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Runner;

use SebastianFeldmann\CaptainHook\Exception;
use SebastianFeldmann\CaptainHook\Hook\Util as HookUtil;
use SebastianFeldmann\CaptainHook\Runner;

/**
 * Class HookHandler
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
abstract class HookHandler extends Runner
{
    /**
     * Hook that should be handled
     *
     * @var string
     */
    protected $hookToHandle;

    /**
     * Hook setter.
     *
     * @param  string $hook
     * @return \SebastianFeldmann\CaptainHook\Runner\HookHandler
     * @throws \SebastianFeldmann\CaptainHook\Exception\InvalidHookName
     */
    public function setHook(string $hook) : HookHandler
    {
        if (!empty($hook) && !HookUtil::isValid($hook)) {
            throw new Exception\InvalidHookName('Invalid hook name \'' . $hook . '\'');
        }
        $this->hookToHandle = $hook;
        return $this;
    }
}
