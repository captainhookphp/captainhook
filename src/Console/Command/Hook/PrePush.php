<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Console\Command\Hook;

use HookMeUp\Console\Command\Hook;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class PrePush
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
class PrePush extends Hook
{
    /**
     * Hook to execute.
     *
     * @var string
     */
    protected $hookName = 'pre-push';

    /**
     * Configure the command.
     */
    protected function configure()
    {
        parent::configure();
        $this->addArgument('target', InputArgument::OPTIONAL, 'Target repository name');
        $this->addArgument('url', InputArgument::OPTIONAL, 'Target repository url');
    }
}
