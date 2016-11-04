<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Hook\PHP\CoverageResolver;

use RuntimeException;
use sebastianfeldmann\CaptainHook\Hook\PHP\CoverageResolver;
use Symfony\Component\Process\Process;

/**
 * Class PHPUnit
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 1.2.0
 */
class PHPUnit implements CoverageResolver
{
    /**
     * Path to phpunit
     *
     * @var string
     */
    private $phpUnit;

    /**
     * PHPUnit constructor.
     *
     * @param string $pathToPHPUnit
     */
    public function __construct($pathToPHPUnit)
    {
        $this->phpUnit = $pathToPHPUnit;
    }

    /**
     * Run PHPUnit to calculate code coverage.
     * Shamelessly ripped from bruli/php-git-hooks.
     *
     * @author Pablo Braulio
     * @return float
     */
    public function getCoverage() : float
    {
        $process = new Process($this->phpUnit . ' --coverage-text|grep Classes|cut -d " " -f 4|cut -d "%" -f 1');
        $process->run();
        $output = $process->getOutput();
        if (!$process->isSuccessful() || empty($output)) {
            throw new RuntimeException('error while executing PHPUnit: ' . $process->getErrorOutput());
        }
        return (float) $output;
    }
}
