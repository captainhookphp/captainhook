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

use CaptainHook\App\Git;

/**
 * Changed Files Placeholder
 *
 * This placeholder only works for pre-push, post-rewrite, post-checkout and post-merge actions.
 * If it is used in a pre-push hook and multiple refs are pushed the placeholder will contain
 * all changed files for all refs.
 *
 * Usage examples:
 *  - {$CHANGED_FILES|separated-by:,}
 *  - {$CHANGED_FILES|in-dir:foo/bar}
 *  - {$CHANGED_FILES|of-type:php}
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.15.3
 */
class ChangedFiles extends CheckFiles
{
    /**
     * @param  array<string, string> $options
     * @return string
     */
    public function replacement(array $options): string
    {
        $files = [];
        foreach (Git\Range\Detector::getRanges($this->io) as $range) {
            $filesInDiff  = isset($options['of-type'])
                          ? $this->repository->getDiffOperator()->getChangedFilesOfType(
                              $range->from()->id(),
                              $range->to()->id(),
                              $options['of-type']
                          )
                          : $this->repository->getDiffOperator()->getChangedFiles(
                              $range->from()->id(),
                              $range->to()->id()
                          );

            $files = array_merge($files, $filesInDiff);
        }
        $files = array_unique($files);
        $files = $this->filterByDirectory($files, $options);
        $files = $this->replaceInAll($files, $options);

        return implode(($options['separated-by'] ?? ' '), $files);
    }
}
