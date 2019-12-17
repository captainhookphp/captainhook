<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\PHP\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO\NullIO;
use CaptainHook\App\Mockery;
use Exception;
use PHPUnit\Framework\TestCase;

class TestCoverageTest extends TestCase
{
    use Mockery;

    /**
     * Tests TestCoverage::execute
     *
     * @throws \Exception
     */
    public function testCoverageViaCloverXML(): void
    {
        if (\defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('not tested on windows');
        }

        $io       = new NullIO();
        $config   = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo     = $this->createRepositoryMock();
        $standard = new TestCoverage();
        $action   = new Config\Action(
            TestCoverage::class,
            ['cloverXml' => CH_PATH_FILES . '/coverage/valid.xml']
        );
        $standard->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
    }

    /**
     * Tests TestCoverage::execute
     *
     * @throws \Exception
     */
    public function testCoverageLow(): void
    {
        $this->expectException(Exception::class);

        $io       = new NullIO();
        $config   = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo     = $this->createRepositoryMock();
        $standard = new TestCoverage();
        $action   = new Config\Action(
            TestCoverage::class,
            [
                'cloverXml'   => CH_PATH_FILES . '/coverage/valid.xml',
                'minCoverage' => 100
            ]
        );
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests TestCoverage::execute
     *
     * @throws \Exception
     */
    public function testCoverageViaPHPUnit(): void
    {
        if (\defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('not tested on windows');
        }

        $io       = new NullIO();
        $config   = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo     = $this->createRepositoryMock();
        $standard = new TestCoverage();
        $action   = new Config\Action(
            TestCoverage::class,
            ['phpUnit' => CH_PATH_FILES . '/bin/phpunit']
        );
        $standard->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
    }
}
