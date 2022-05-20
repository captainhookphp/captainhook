<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\PHP\CoverageResolver;

use RuntimeException;
use CaptainHook\App\Hook\PHP\CoverageResolver;
use CaptainHook\App\Storage\File\Xml;

/**
 * Class CloverXML
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 1.2.0
 */
class CloverXML implements CoverageResolver
{
    /**
     * Clover XML
     *
     * @var \SimpleXMLElement
     */
    private $xml;

    /**
     * CloverXML constructor.
     *
     * @param string $pathToCloverXml
     */
    public function __construct($pathToCloverXml)
    {
        $cloverFile = new Xml($pathToCloverXml);
        if (!$cloverFile->exists()) {
            throw new RuntimeException('could not find clover xml file: ' . $cloverFile->getPath());
        }
        $this->xml = $cloverFile->read();
        $this->validateXml();
    }

    /**
     * Make sure you have a valid xml structure
     *
     * @return void
     * @throws \RuntimeException
     */
    private function validateXml(): void
    {
        if (!isset($this->xml->project) || !isset($this->xml->project->metrics)) {
            throw new RuntimeException('invalid clover xml file');
        }
    }

    /**
     * Return test coverage in percent.
     *
     * @return float
     */
    public function getCoverage(): float
    {
        $statements = (string) $this->xml->project->metrics->attributes()->statements;
        $covered    = (string) $this->xml->project->metrics->attributes()->coveredstatements;

        if ($statements < 1 || !is_numeric($covered)) {
            throw new RuntimeException(
                'could not read coverage data from clover xml file ' .
                '(statements: ' . $statements . ', coveredstatements: ' . $covered . ')'
            );
        }

        return $covered / ((int)$statements * 0.01);
    }
}
