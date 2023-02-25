<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\Input\PrePush;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO\NullIO;
use CaptainHook\App\Exception\ActionFailed;
use CaptainHook\App\Hook\File\Action\MaxSize;
use CaptainHook\App\Hooks;
use CaptainHook\App\Mockery;
use Exception;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class RefTest extends TestCase
{
    /**
     * Tests all ref getter
     *
     */
    public function testGetter(): void
    {
        $ref = new Ref('refs/heads/main', '12345', 'main');

        $this->assertEquals('refs/heads/main', $ref->head());
        $this->assertEquals('12345', $ref->hash());
        $this->assertEquals('main', $ref->branch());
    }
}
