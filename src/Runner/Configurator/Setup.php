<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Runner\Configurator;

use SebastianFeldmann\CaptainHook\Config;

/**
 * Interface Setup
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 2.1.3
 */
interface Setup
{
    /**
     * Setup hook configurations by asking some questions
     *
     * @param  \SebastianFeldmann\CaptainHook\Config $config
     * @return void
     */
    public function configureHooks(Config $config);
}
