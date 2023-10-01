<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace CaptainHook\App\Hook\Template;

use CaptainHook\App\Config\Mockery as ConfigMockery;
use CaptainHook\App\Git\DummyRepo;
use CaptainHook\App\Mockery as AppMockery;
use Exception;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{
    use AppMockery;
    use ConfigMockery;

    /**
     * Tests Builder::build
     */
    public function testBuildDockerTemplate(): void
    {
        $repo = new DummyRepo(
            [],
            [
                'captainhook.json' => '{}',
                'vendor' => [
                    'autoload.php' => '',
                    'bin' => [
                        'captainhook' => ''
                    ]
                ]
            ]
        );

        $resolver   = $this->createResolverMock($repo->getRoot() . '/vendor/bin/captainhook', false);
        $repository = $this->createRepositoryMock($repo->getRoot());
        $config     = $this->createConfigMock(true, $repo->getRoot() . '/captainhook.json');
        $config->method('getRunMode')->willReturn('docker');
        $config->method('getRunExec')->willReturn('docker exec captain-container');
        $config->method('getRunPath')->willReturn('');
        $config->method('getBootstrap')->willReturn('vendor/autoload.php');

        $template = Builder::build($config, $repository, $resolver);
        $this->assertInstanceOf(Docker::class, $template);

        $code = $template->getCode('pre-commit');
        $this->assertStringContainsString('pre-commit', $code);
        $this->assertStringContainsString('docker exec -i -e GIT_INDEX_FILE captain-container', $code);
        $this->assertStringContainsString('vendor/bin/captainhook', $code);
    }

    /**
     * Tests Builder::build
     */
    public function testBuildDockerTemplateWithBinaryOutsideRepo(): void
    {
        $repo = new DummyRepo(
            [],
            [
                'captainhook.json' => '{}',
                'vendor' => [
                    'autoload.php' => '',
                ]
            ]
        );

        $executable = realpath(__DIR__ . '/../../../../bin/captainhook');
        $resolver   = $this->createResolverMock($executable, false);
        $repository = $this->createRepositoryMock($repo->getRoot());
        $config     = $this->createConfigMock(true, $repo->getRoot() . '/captainhook.json');
        $config->method('getRunMode')->willReturn('docker');
        $config->method('getRunExec')->willReturn('docker exec captain-container');
        $config->method('getRunPath')->willReturn('');
        $config->method('getBootstrap')->willReturn('vendor/autoload.php');

        $template = Builder::build($config, $repository, $resolver);
        $code     = $template->getCode('pre-commit');

        $this->assertInstanceOf(Docker::class, $template);
        $this->assertStringContainsString('pre-commit', $code);
        $this->assertStringContainsString('docker exec -i -e GIT_INDEX_FILE captain-container', $code);
        $this->assertStringContainsString($executable, $code);
    }

    /**
     * Tests Builder::build
     */
    public function testBuildLocalTemplate(): void
    {
        $resolver   = $this->createResolverMock(CH_PATH_FILES . '/bin/captainhook', false);
        $repository = $this->createRepositoryMock(CH_PATH_FILES);
        $config     = $this->createConfigMock(true, CH_PATH_FILES . '/template/captainhook.json');
        $config->method('getRunMode')->willReturn('php');
        $config->method('getRunExec')->willReturn('');
        $config->method('getBootstrap')->willReturn('vendor/autoload.php');

        $template = Builder::build($config, $repository, $resolver);
        $this->assertInstanceOf(Local\PHP::class, $template);

        $code = $template->getCode('pre-commit');
        $this->assertStringContainsString('pre-commit', $code);
        $this->assertStringContainsString('$captainHook->run', $code);
    }

    /**
     * Tests Builder::build
     */
    public function testBuildInvalidVendor(): void
    {
        $this->expectException(Exception::class);

        $resolver   = $this->createResolverMock('./captainhook', false);
        $repository = $this->createRepositoryMock(CH_PATH_FILES . '/config');
        $config     = $this->createConfigMock(true, CH_PATH_FILES . '/config/valid.json');
        $config->method('getRunMode')->willReturn('php');
        $config->method('getRunExec')->willReturn('');
        $config->method('getBootstrap')->willReturn('file-not-there.php');

        Builder::build($config, $repository, $resolver);
    }
}
