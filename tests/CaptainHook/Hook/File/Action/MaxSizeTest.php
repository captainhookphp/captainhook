<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\File\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO\NullIO;
use CaptainHook\App\Exception\ActionFailed;
use CaptainHook\App\Hooks;
use CaptainHook\App\Mockery;
use PHPUnit\Framework\TestCase;

class MaxSizeTest extends TestCase
{
    use Mockery;

    /**
     * Tests MaxSite::getRestriction
     */
    public function testRestrictions(): void
    {
        $restriction = MaxSize::getRestriction();

        $this->assertTrue($restriction->isApplicableFor(Hooks::PRE_COMMIT));
        $this->assertFalse($restriction->isApplicableFor(Hooks::POST_CHECKOUT));
    }

    /**
     * Tests MaxSize::execute
     *
     * @throws \Exception
     */
    public function testPass(): void
    {
        $io       = new NullIO();
        $config   = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo     = $this->createRepositoryMock();
        $files    = [CH_PATH_FILES . '/config/valid.json', CH_PATH_FILES . '/config/valid-with-all-settings.json'];
        $operator = $this->createGitIndexOperator($files);
        $repo->expects($this->once())->method('getIndexOperator')->willReturn($operator);

        $action   = new Config\Action(MaxSize::class, ['maxSize' => '1M']);
        $standard = new MaxSize();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests MaxSize::execute
     *
     * @throws \Exception
     */
    public function testFail(): void
    {
        $this->expectException(ActionFailed::class);

        $io       = new NullIO();
        $config   = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo     = $this->createRepositoryMock();
        $files    = [CH_PATH_FILES . '/config/empty.json', 'fooBarBaz'];
        $operator = $this->createGitIndexOperator($files);
        $repo->expects($this->once())->method('getIndexOperator')->willReturn($operator);

        $action   = new Config\Action(MaxSize::class, ['maxSize' => '1B']);
        $standard = new MaxSize();
        $standard->execute($config, $io, $repo, $action);
    }
}
