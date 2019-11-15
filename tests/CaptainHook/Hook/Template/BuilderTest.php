<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace CaptainHook\App\Hook\Template;

use CaptainHook\App\Config;
use PHPUnit\Framework\TestCase;
use SebastianFeldmann\Git\Repository;

class BuilderTest extends TestCase
{
    /**
     * Tests Builder::build
     */
    public function testBuildDockerTemplate(): void
    {
        $config = $this->prophesize(Config::class);
        $config->getRunMode()->willReturn('docker');
        $config->getRunExec()->willReturn('docker exec captain-container');
        $config->getPath()->willReturn(CH_PATH_FILES . '/template/captainhook.json');
        $config->getVendorDirectory()->willReturn(CH_PATH_FILES . '/template/vendor');

        $repository = $this->prophesize(Repository::class);
        $repository->getRoot()->willReturn(CH_PATH_FILES . '/template');

        $template = Builder::build($config->reveal(), $repository->reveal());
        $this->assertInstanceOf(Docker::class, $template);

        $code = $template->getCode('pre-commit');
        $this->assertStringContainsString('pre-commit', $code);
        $this->assertStringContainsString('docker exec captain-container', $code);
        $this->assertStringContainsString('./vendor/bin/captainhook-run', $code);
    }

    /**
     * Tests Builder::build
     */
    public function testBuildDockerTemplateObservesConfig(): void
    {
        $vendorPath = realpath(__DIR__ . '/../../../../vendor');
        $config     = $this->prophesize(Config::class);
        $config->getRunMode()->willReturn('docker');
        $config->getRunExec()->willReturn('docker exec captain-container');
        $config->getPath()->willReturn(CH_PATH_FILES . '/config/valid.json');
        $config->getVendorDirectory()->willReturn($vendorPath);

        $repository = $this->prophesize(Repository::class);
        $repository->getRoot()->willReturn(CH_PATH_FILES . '/config');

        $template = Builder::build($config->reveal(), $repository->reveal());
        $this->assertInstanceOf(Docker::class, $template);

        $code = $template->getCode('pre-commit');
        $this->assertStringContainsString('pre-commit', $code);
        $this->assertStringContainsString('docker exec captain-container', $code);
        $this->assertStringContainsString($vendorPath . '/bin/captainhook-run', $code);
    }

    /**
     * Tests Builder::build
     */
    public function testBuildLocalTemplate(): void
    {
        $config = $this->prophesize(Config::class);
        $config->getRunMode()->willReturn('local');
        $config->getRunExec()->willReturn('');
        $config->getPath()->willReturn(CH_PATH_FILES . '/config/valid.json');
        $config->getVendorDirectory()->willReturn('vendor');

        $repository = $this->prophesize(Repository::class);
        $repository->getRoot()->willReturn(CH_PATH_FILES . '/config');

        $template = Builder::build($config->reveal(), $repository->reveal());
        $this->assertInstanceOf(Local::class, $template);

        $code = $template->getCode('pre-commit');
        $this->assertStringContainsString('pre-commit', $code);
        $this->assertStringContainsString('$app->setHook', $code);
    }

    /**
     * Tests Builder::build
     */
    public function testBuildInvalidVendor(): void
    {
        $this->expectException(\Exception::class);

        $config = $this->prophesize(Config::class);
        $config->getRunMode()->willReturn('local');
        $config->getRunExec()->willReturn('');
        $config->getPath()->willReturn(CH_PATH_FILES . '/config/valid.json');
        $config->getVendorDirectory()->willReturn('vendor-fail');

        $repository = $this->prophesize(Repository::class);
        $repository->getRoot()->willReturn(CH_PATH_FILES . '/config');

        $template = Builder::build($config->reveal(), $repository->reveal());
    }
}
