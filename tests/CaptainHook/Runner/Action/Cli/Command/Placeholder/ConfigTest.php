<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner\Action\Cli\Command\Placeholder;

use CaptainHook\App\Config\Mockery as ConfigMockery;
use CaptainHook\App\Mockery as AppMockery;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    use AppMockery;
    use ConfigMockery;

    /**
     * Tests Config::replacement
     */
    public function testConfigValue(): void
    {
        $repo   = $this->createRepositoryMock();
        $config = $this->createConfigMock();
        $config->expects($this->once())->method('getGitDirectory')->willReturn('./.git');

        $placeholder = new Config($config, $repo);
        $gitDir      = $placeholder->replacement(['value-of' => 'git-directory']);

        $this->assertEquals('./.git', $gitDir);
    }

    /**
     * Tests Config::replacement
     */
    public function testNoValueOf(): void
    {
        $repo   = $this->createRepositoryMock();
        $config = $this->createConfigMock();

        $placeholder = new Config($config, $repo);
        $gitDir      = $placeholder->replacement([]);

        $this->assertEquals('', $gitDir);
    }

    /**
     * Tests Config::replacement
     */
    public function testInvalidConfigValue(): void
    {
        $repo   = $this->createRepositoryMock();
        $config = $this->createConfigMock();

        $placeholder = new Config($config, $repo);
        $gitDir      = $placeholder->replacement(['value-of' => 'includes']);

        $this->assertEquals('', $gitDir);
    }
}
