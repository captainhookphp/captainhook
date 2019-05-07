<?php
declare(strict_types=1);

namespace CaptainHook\App\Hook;

use CaptainHook\App\Config;
use CaptainHook\App\Hook\Template\Docker;
use CaptainHook\App\Hook\Template\Local;
use PHPUnit\Framework\TestCase;
use SebastianFeldmann\Git\Repository;
use Symfony\Component\Console\Input\InputInterface;

class TemplateBuilderTest extends TestCase
{
    public function testBuildDockerTemplate(): void
    {
        $input = $this->prophesize(InputInterface::class);
        $input->getOption('run-mode')->willReturn('docker');
        $input->getOption('container-name')->willReturn('captain-container');

        $config = $this->prophesize(Config::class);
        $config->getPath()->willReturn(__DIR__ . '/../../files/config/valid.json');

        $repository = $this->prophesize(Repository::class);
        $repository->getRoot()->willReturn(__DIR__ . '/../../files/config');

        $template = TemplateBuilder::build($input->reveal(), $config->reveal(), $repository->reveal());
        $this->assertInstanceOf(Docker::class, $template);

        $code = $template->getCode('pre-commit');
        $this->assertStringContainsString('pre-commit', $code);
        $this->assertStringContainsString('captain-container', $code);
    }

    public function testBuildLocalTemplate(): void
    {
        $input = $this->prophesize(InputInterface::class);
        $input->getOption('run-mode')->willReturn('local');

        $config = $this->prophesize(Config::class);
        $config->getPath()->willReturn(__DIR__ . '/../../files/config/valid.json');

        $repository = $this->prophesize(Repository::class);
        $repository->getRoot()->willReturn(__DIR__ . '/../../files/config');

        $template = TemplateBuilder::build($input->reveal(), $config->reveal(), $repository->reveal());
        $this->assertInstanceOf(Local::class, $template);

        $code = $template->getCode('pre-commit');
        $this->assertStringContainsString('pre-commit', $code);
        $this->assertStringContainsString('$app->setHook', $code);
    }
}