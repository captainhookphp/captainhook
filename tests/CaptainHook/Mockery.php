<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App;

use SebastianFeldmann\Git\Operator\Info;
use SebastianFeldmann\Git\Repository;

trait Mockery
{
    /**
     * Create repository mock
     *
     * @param  string $root
     * @return \SebastianFeldmann\Git\Repository
     */
    public function createRepositoryMock(string $root = '')
    {
        $repo = $this->getMockBuilder(Repository::class)
                       ->disableOriginalConstructor()
                       ->getMock();

        $repo->method('getRoot')->willReturn($root);
        $repo->method('getHooksDir')->willReturn($root . '/.git/hooks');

        return $repo;
    }

    /**
     * Create info operator mock
     *
     * @param  string $tag
     * @return \SebastianFeldmann\Git\Operator\Info
     */
    public function createGitInfoOperator(string $tag = 'v1.0.0')
    {
        $operator = $this->getMockBuilder(Info::class)
                         ->disableOriginalConstructor()
                         ->getMock();

        $operator->method('getCurrentTag')->willReturn($tag);

        return $operator;
    }
}
