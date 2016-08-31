<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\App\Runner;

use HookMeUp\App\Exception;
use HookMeUp\App\Hook\Util as HookUtil;
use HookMeUp\App\Runner;

/**
 * Class HookHandler
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
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
     * @return \HookMeUp\App\Runner\HookHandler
     * @throws \HookMeUp\App\Exception\InvalidHookName
     */
    public function setHook($hook)
    {
        if (null !== $hook && !HookUtil::isValid($hook)) {
            throw new Exception\InvalidHookName('Invalid hook name \'' . $hook . '\'');
        }
        $this->hookToHandle = $hook;
        return $this;
    }
}
