<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Hook\PHP\Action;

use SebastianFeldmann\CaptainHook\Config;
use SebastianFeldmann\CaptainHook\Console\IO;
use SebastianFeldmann\CaptainHook\Exception\ActionFailed;
use SebastianFeldmann\CaptainHook\Hook\Action;
use SebastianFeldmann\CaptainHook\Hook\PHP\CoverageResolver;
use SebastianFeldmann\Git\Repository;

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
     * @param  \SebastianFeldmann\CaptainHook\Config         $config
     * @param  \SebastianFeldmann\CaptainHook\Console\IO     $io
     * @param  \SebastianFeldmann\Git\Repository             $repository
     * @param  \SebastianFeldmann\CaptainHook\Config\Action  $action
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
     * @param  \SebastianFeldmann\CaptainHook\Config\Options $options
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
     * @return \SebastianFeldmann\CaptainHook\Hook\PHP\CoverageResolver
     */
    protected function getCoverageResolver() : CoverageResolver
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
     * @throws \SebastianFeldmann\CaptainHook\Exception\ActionFailed
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
