<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\App\Console\Command\Hook;

use HookMeUp\App\Console\Command\Hook;

/**
 * Class PreCommit
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
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
