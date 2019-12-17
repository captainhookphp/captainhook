<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook;

trait Mockery
{
    /**
     * Create Template mock
     *
     * @return \CaptainHook\App\Hook\Template&\PHPUnit\Framework\MockObject\MockObject
     */
    public function createTemplateMock(): Template
    {
        return $this->getMockBuilder(Template\Local::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @param  $type
     * @return \PHPUnit\Framework\MockObject\MockBuilder
     */
    public abstract function getMockBuilder($type);
}
