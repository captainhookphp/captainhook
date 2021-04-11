<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner\Hook;

use CaptainHook\App\Config\Mockery as ConfigMockery;
use CaptainHook\App\Console\IO\Mockery as IOMockery;
use CaptainHook\App\Mockery as CHMockery;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

class PostCheckoutTest extends TestCase
{
    use ConfigMockery;
    use IOMockery;
    use CHMockery;

    /**
     * Tests PostCheckout::run
     *
     * @throws \Exception
     */
    public function testRunHookEnabled(): void
    {
        $io            = $this->createIOMock();
        $config        = $this->createConfigMock();
        $repo          = $this->createRepositoryMock();
        $hookConfig    = $this->createHookConfigMock();
        $actionConfig1 = $this->createActionConfigMock();
        $actionConfig2 = $this->createActionConfigMock();
        $hookConfig->expects($this->once())->method('isEnabled')->willReturn(true);
        $hookConfig->expects($this->once())->method('getActions')->willReturn([$actionConfig1, $actionConfig2]);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->atLeast(1))->method('write');

        // Ensure that our actions are processed.
        $actionConfig1->expects($this->atLeast(1))->method('getAction');
        $actionConfig1->expects($this->atLeast(1))->method('getConditions');
        $actionConfig2->expects($this->atLeast(1))->method('getAction');
        $actionConfig2->expects($this->atLeast(1))->method('getConditions');

        $runner = new PostCheckout($io, $config, $repo);
        $runner->run();
    }

    /**
     * Tests PostCheckout::run
     *
     * @throws \Exception
     */
    public function testRunHookDisabled(): void
    {
        $io           = $this->createIOMock();
        $config       = $this->createConfigMock();
        $hookConfig   = $this->createHookConfigMock();
        $repo         = $this->createRepositoryMock();
        $hookConfig->expects($this->once())->method('isEnabled')->willReturn(false);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->once())->method('write');

        $runner = new PostCheckout($io, $config, $repo);
        $runner->run();
    }

    public function testRunHookSkipsActionsWhenEnvVarPresent(): void
    {
        // This should not be present when starting this test.
        $this->assertFalse(getenv(PostCheckout::SKIP_POST_CHECKOUT_VAR));

        $io            = $this->createIOMock();
        $config        = $this->createConfigMock();
        $repo          = $this->createRepositoryMock();
        $hookConfig    = $this->createHookConfigMock();
        $actionConfig1 = $this->createActionConfigMock();
        $actionConfig2 = $this->createActionConfigMock();
        $hookConfig->expects($this->once())->method('isEnabled')->willReturn(true);
        $hookConfig->expects($this->once())->method('getActions')->willReturn([$actionConfig1, $actionConfig2]);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->atLeast(1))->method('write');

        // Ensure that our actions are never processed.
        $actionConfig1->expects($this->never())->method('getAction');
        $actionConfig1->expects($this->never())->method('getConditions');
        $actionConfig2->expects($this->never())->method('getAction');
        $actionConfig2->expects($this->never())->method('getConditions');

        // Inject the environment variable that should cause
        // post-checkout to skip all configured actions.
        putenv(PostCheckout::SKIP_POST_CHECKOUT_VAR . '=1');

        $runner = new PostCheckout($io, $config, $repo);
        $runner->run();

        // Now that we're done, unset the environment variable so it doesn't
        // confuse any other tests.
        putenv(PostCheckout::SKIP_POST_CHECKOUT_VAR);
    }

    /**
     * We test this protected method to ensure it properly sets the environment
     * variables before calling the callable and then unsets them afterwards.
     */
    public function testCallWithEnvironment(): void
    {
        $config = $this->createConfigMock();
        $io = $this->createIOMock();
        $repo = $this->createRepositoryMock();

        $runner = new PostCheckout($io, $config, $repo);

        $reflectedRunner = new ReflectionObject($runner);
        $callWithEnvironment = $reflectedRunner->getMethod('callWithEnvironment');
        $callWithEnvironment->setAccessible(true);

        $callable = function (): void {
            // When called inside the method, the variables should be set.
            $this->assertSame('1', getenv(PostCheckout::SKIP_POST_CHECKOUT_VAR));
            $this->assertSame('foobar', getenv('FOO_BAR_BAZ'));
        };

        // These variables should not be set before calling the method.
        $this->assertFalse(getenv(PostCheckout::SKIP_POST_CHECKOUT_VAR));
        $this->assertFalse(getenv('FOO_BAR_BAZ'));

        $callWithEnvironment->invoke($runner, $callable, [
            PostCheckout::SKIP_POST_CHECKOUT_VAR => 1,
            'FOO_BAR_BAZ' => 'foobar',
        ]);

        // These variables should not be set after calling the method.
        $this->assertFalse(getenv(PostCheckout::SKIP_POST_CHECKOUT_VAR));
        $this->assertFalse(getenv('FOO_BAR_BAZ'));
    }
}
