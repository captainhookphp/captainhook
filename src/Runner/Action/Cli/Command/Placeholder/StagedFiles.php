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

use CaptainHook\App\Runner\Action\Cli\Command\Placeholder;
use SebastianFeldmann\Git\Repository;

/**
 * Class UpdatedFiles
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.0.0
 */
class StagedFiles implements Placeholder
{
    /**
     * Git repository
     *
     * @var \SebastianFeldmann\Git\Repository
     */
    private $repository;

    /**
     * UpdatedFiles constructor
     *
     * @param \SebastianFeldmann\Git\Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param  array $options
     * @return string
     */
    public function replacement(array $options): string
    {
        $files = isset($options['of-type'])
               ? $this->repository->getIndexOperator()->getStagedFilesOfType($options['of-type'])
               : $this->repository->getIndexOperator()->getStagedFiles();

        return implode(($options['separated-by'] ?? ' '), $files);
    }
}
