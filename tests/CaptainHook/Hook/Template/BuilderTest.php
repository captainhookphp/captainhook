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
use Symfony\Component\Console\Input\InputInterface;

class BuilderTest extends TestCase
{
    /**
     * Tests Builder::build
     */
    public function testBuildDockerTemplate(): void
    {
        $input = $this->prophesize(InputInterface::class);
        $input->getOption('run-mode')->willReturn('docker');
        $input->getOption('run-exec')->willReturn('docker exec captain-container');

        $config = $this->prophesize(Config::class);
        $config->getPath()->willReturn(CH_PATH_FILES . '/config/valid.json');

        $repository = $this->prophesize(Repository::class);
        $repository->getRoot()->willReturn(CH_PATH_FILES . '/config');

        $template = Builder::build($input->reveal(), $config->reveal(), $repository->reveal());
        $this->assertInstanceOf(Docker::class, $template);

        $code = $template->getCode('pre-commit');
        $this->assertStringContainsString('pre-commit', $code);
        $this->assertStringContainsString('docker exec captain-container', $code);
    }

    /**
     * Tests Builder::build
     */
    public function testBuildLocalTemplate(): void
    {
        $input = $this->prophesize(InputInterface::class);
        $input->getOption('run-mode')->willReturn('local');

        $config = $this->prophesize(Config::class);
        $config->getPath()->willReturn(CH_PATH_FILES . '/config/valid.json');

        $repository = $this->prophesize(Repository::class);
        $repository->getRoot()->willReturn(CH_PATH_FILES . '/config');

        $template = Builder::build($input->reveal(), $config->reveal(), $repository->reveal());
        $this->assertInstanceOf(Local::class, $template);

        $code = $template->getCode('pre-commit');
        $this->assertStringContainsString('pre-commit', $code);
        $this->assertStringContainsString('$app->setHook', $code);
    }
}
