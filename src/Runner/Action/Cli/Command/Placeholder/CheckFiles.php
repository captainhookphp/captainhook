<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner\Action\Cli\Command\Placeholder;

/**
 * Class CheckFiles
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.0.0
 */
abstract class CheckFiles extends Foundation
{
    /**
     * Filter staged files by directory
     *
     * @param  array<string>         $files
     * @param  array<string, string> $options
     * @return array<string>
     */
    protected function filterByDirectory(array $files, array $options): array
    {
        if (!isset($options['in-dir'])) {
            return $files;
        }

        $directory = $options['in-dir'];
        $filtered  = [];
        foreach ($files as $file) {
            if (strpos($file, $directory, 0) === 0) {
                $filtered[] = $file;
            }
        }

        return $filtered;
    }

    /**
     * Run search replace for all files
     *
     * @param  array<string>         $files
     * @param  array<string, string> $options
     * @return array<string>
     */
    protected function replaceInAll(array $files, array $options): array
    {
        if (!isset($options['replace'])) {
            return $files;
        }

        $search  = $options['replace'];
        $replace = $options['with'] ?? '';

        foreach ($files as $index => $file) {
            $files[$index] = str_replace($search, $replace, $file);
        }
        return $files;
    }
}
