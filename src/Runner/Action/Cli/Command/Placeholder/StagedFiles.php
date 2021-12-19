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
 * Class UpdatedFiles
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.0.0
 */
class StagedFiles extends Foundation
{
    /**
     * @param  array<string, string> $options
     * @return string
     */
    public function replacement(array $options): string
    {
        $filter = isset($options['diff-filter']) ? str_split($options['diff-filter']) : ['A', 'C', 'M', 'R'];
        $files  = isset($options['of-type'])
                ? $this->repository->getIndexOperator()->getStagedFilesOfType($options['of-type'], $filter)
                : $this->repository->getIndexOperator()->getStagedFiles($filter);

        $files = $this->filterByDirectory($files, $options);
        $files = $this->replaceInAll($files, $options);

        return implode(($options['separated-by'] ?? ' '), $files);
    }

    /**
     * Filter staged files by directory
     *
     * @param  array<string> $files
     * @param  array<string, string> $options
     * @return array<string>
     */
    private function filterByDirectory(array $files, array $options): array
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
     * @param  array<string> $files
     * @param  array<string, string> $options
     * @return array<string>
     */
    private function replaceInAll(array $files, array $options): array
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
