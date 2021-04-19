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

class EnvTest extends TestCase
{
    use AppMockery;
    use ConfigMockery;

    /**
     * Tests Env::replacement
     */
    public function testEnvValue(): void
    {
        $_ENV['foo'] = 'bar';
        $repo   = $this->createRepositoryMock();
        $config = $this->createConfigMock();

        $placeholder = new Env($config, $repo);
        $result      = $placeholder->replacement(['value-of' => 'foo']);

        $this->assertEquals('bar', $result);

        unset($_ENV['foo']);
    }

    /**
     * Tests Env::replacement
     */
    public function testNoValueOf(): void
    {
        $repo   = $this->createRepositoryMock();
        $config = $this->createConfigMock();

        $placeholder = new Env($config, $repo);
        $result      = $placeholder->replacement([]);

        $this->assertEquals('', $result);
    }

    /**
     * Tests Env::replacement
     */
    public function testDefault(): void
    {
        $repo   = $this->createRepositoryMock();
        $config = $this->createConfigMock();

        $placeholder = new Env($config, $repo);
        $result      = $placeholder->replacement(['value-of' => 'MY_SUPER_ENV_VAR', 'default' => 'my-default']);

        $this->assertEquals('my-default', $result);
    }
}
