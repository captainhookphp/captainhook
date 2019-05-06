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

/**
 * Template class
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
abstract class Template
{
    /**
     * Return the php code for the git hook scripts.
     *
     * @param string $hook Name of the hook to trigger.
     * @param string $containerName Name of the container to run
     *
     * @return string
     */
    public static function getCode(string $hook, string $containerName) : string
    {
        return '#!/usr/bin/env bash' . PHP_EOL .
            'docker exec ' . $containerName . ' php .git/hooks/captain-runner.php ' . $hook . ' "$@"';
    }
}
