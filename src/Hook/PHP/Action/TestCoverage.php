<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Hook\PHP\Action;

use sebastianfeldmann\CaptainHook\Config;
use sebastianfeldmann\CaptainHook\Console\IO;
use sebastianfeldmann\CaptainHook\Exception\ActionFailed;
use sebastianfeldmann\CaptainHook\Git\Repository;
use sebastianfeldmann\CaptainHook\Hook\Action;
use sebastianfeldmann\CaptainHook\Hook\PHP\CoverageResolver;

/**
 * Class TestCoverage
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 1.2.0
 */
class TestCoverage implements Action
{
    /**
     * Clover XML file
     *
     * @var string
     */
    private $cloverXmlFile;

    /**
     * Path to PHPUnit
     *
     * @var string
     */
    private $phpUnit;

    /**
     * Minimum coverage in percent
     *
     * @var float
     */
    private $minCoverage;

    /**
     * Executes the action.
     *
     * @param  \sebastianfeldmann\CaptainHook\Config         $config
     * @param  \sebastianfeldmann\CaptainHook\Console\IO     $io
     * @param  \sebastianfeldmann\CaptainHook\Git\Repository $repository
     * @param  \sebastianfeldmann\CaptainHook\Config\Action  $action
     * @throws \Exception
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action)
    {
        $io->write('checking coverage:', true, IO::VERBOSE);
        $this->handleOptions($action->getOptions());

        $coverageResolver = $this->getCoverageResolver();
        $coverage         = $coverageResolver->getCoverage();

        $this->verifyCoverage($coverage);
        $io->write('<info>test coverage: ' . $coverage . '%</info>');
    }

    /**
     * Setup local properties with given options.
     *
     * @param  \sebastianfeldmann\CaptainHook\Config\Options $options
     * @throws \RuntimeException
     */
    protected function handleOptions(Config\Options $options)
    {
        $this->cloverXmlFile = $options->get('cloverXml');
        $this->phpUnit       = $options->get('phpUnit', 'phpunit');
        $this->minCoverage   = $options->get('minCoverage', 80);
    }

    /**
     * Return the adequate coverage resolver.
     *
     * @return \sebastianfeldmann\CaptainHook\Hook\PHP\CoverageResolver
     */
    protected function getCoverageResolver()
    {
        // if clover xml is configured use it to read coverage data
        if (null !== $this->cloverXmlFile) {
            return new CoverageResolver\CloverXML($this->cloverXmlFile);
        }

        // no clover xml so use phpunit to get current test coverage
        return new CoverageResolver\PHPUnit($this->phpUnit);
    }

    /**
     * Check if current coverage is high enough.
     *
     * @param  float $coverage
     * @throws \sebastianfeldmann\CaptainHook\Exception\ActionFailed
     */
    protected function verifyCoverage($coverage)
    {
        if ($coverage < $this->minCoverage) {
            throw ActionFailed::withMessage(
                'Test coverage to low!' . PHP_EOL .
                'Current coverage is at ' . $coverage . '% but should be at least ' . $this->minCoverage . '%'
            );
        }
    }
}
