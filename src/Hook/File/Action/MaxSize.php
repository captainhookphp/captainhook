<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\File\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Exception\ActionFailed;
use CaptainHook\App\Hook\Action;
use CaptainHook\App\Hook\Constrained;
use CaptainHook\App\Hook\Restriction;
use CaptainHook\App\Hooks;
use RuntimeException;
use SebastianFeldmann\Git\Repository;

/**
 * MaxSize
 *
 * Check all staged files for file size
 *
 * {
 *     "action": "\\CaptainHook\\App\\Hook\\File\\Action\\MaxSize",
 *     "options": {
 *         "size" : "5M"
 *     }
 * }
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.4.3
 */
class MaxSize implements Action, Constrained
{
    /**
     * @var int
     */
    private $maxBytes;

    /**
     * Returns a list of applicable hooks
     *
     * @return \CaptainHook\App\Hook\Restriction
     */
    public static function getRestriction(): Restriction
    {
        return Restriction::fromArray([Hooks::PRE_COMMIT]);
    }

    /**
     * Executes the action
     *
     * @param  \CaptainHook\App\Config           $config
     * @param  \CaptainHook\App\Console\IO       $io
     * @param  \SebastianFeldmann\Git\Repository $repository
     * @param  \CaptainHook\App\Config\Action    $action
     * @return void
     * @throws \Exception
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action): void
    {
        $filesStaged    = $repository->getIndexOperator()->getStagedFiles();
        $filesFailed    = 0;
        $this->maxBytes = $this->toBytes($action->getOptions()->get('maxSize'));

        foreach ($filesStaged as $file) {
            if ($this->isTooBig($file)) {
                $filesFailed++;
                $io->write('- <error>FAIL</error> ' . $file, true);
            } else {
                $io->write('- <info>OK</info> ' . $file, true, IO::VERBOSE);
            }
        }

        if ($filesFailed > 0) {
            $text = $filesFailed > 1 ? ' files were too big' : 'file is too big';
            throw new ActionFailed('<error>Error: ' . $filesFailed . ' ' . $text . '</error>');
        }

        $io->write('<info>File size is ok</info>');
    }

    /**
     * Compare a file to configured max file size
     *
     * @param  string $file
     * @return bool
     */
    private function isTooBig(string $file): bool
    {
        if (!file_exists($file) || is_dir($file)) {
            return false;
        }

        if (filesize($file) > $this->maxBytes) {
            return true;
        }

        return false;
    }

    /**
     * Return given size in bytes
     * Allowed units:
     *   B => byte
     *   K => kilo byte
     *   M => mega byte
     *   G => giga byte
     *   T => terra byte
     *   P => peta byte
     *
     * e.g.
     * 1K => 1024
     * 2K => 2048
     * ...
     *
     * @param  string $value
     * @throws \RuntimeException
     * @return int
     */
    public function toBytes(string $value): int
    {
        if (!preg_match('#^[0-9]*[BKMGTP]$#i', $value)) {
            throw new RuntimeException('Invalid size value');
        }
        $units  = ['B' => 0, 'K' => 1, 'M' => 2, 'G' => 3, 'T' => 4, 'P' => 5];
        $unit   = strtoupper(substr($value, -1));
        $number = intval(substr($value, 0, -1));

        return $number * pow(1024, $units[$unit]);
    }
}
