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

interface Template
{
    public const LOCAL = 'local';
    public const DOCKER = 'docker';

    /**
     * Return the code for the git hook scripts.
     *
     * @param string $hook Name of the hook to trigger.
     *
     * @return string
     */
    public function getCode(string $hook): string;
}
