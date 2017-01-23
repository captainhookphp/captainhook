<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Console\Command\Hook;

use SebastianFeldmann\CaptainHook\Console\Command\Hook;

/**
 * Class PreCommit
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class PreCommit extends Hook
{
    /**
     * Hook to execute.
     *
     * @var string
     */
    protected $hookName = 'pre-commit';
}
