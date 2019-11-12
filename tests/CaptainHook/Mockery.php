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

use SebastianFeldmann\Git\Operator\Diff;
use SebastianFeldmann\Git\Operator\Index;
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
    public function createRepositoryMock(string $root = ''): Repository
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
    public function createGitInfoOperator(string $tag = 'v1.0.0'): Info
    {
        $operator = $this->getMockBuilder(Info::class)
                         ->disableOriginalConstructor()
                         ->getMock();

        $operator->method('getCurrentTag')->willReturn($tag);

        return $operator;
    }

    /**
     * Create diff operator mock
     *
     * @param  array $changedFiles
     * @return \SebastianFeldmann\Git\Operator\Diff
     */
    public function createGitDiffOperator(array $changedFiles = []): Diff
    {
        $operator = $this->getMockBuilder(Diff::class)
                         ->disableOriginalConstructor()
                         ->getMock();

        $operator->method('getChangedFiles')->willReturn($changedFiles);

        return $operator;
    }

    /**
     * Create index operator mock
     *
     * @param  array $stagedFiles
     * @return \SebastianFeldmann\Git\Operator\Index
     */
    public function createGitIndexOperator(array $stagedFiles = []): Index
    {
        $operator = $this->getMockBuilder(Index::class)
                         ->disableOriginalConstructor()
                         ->getMock();

        $operator->method('getStagedFiles')->willReturn($stagedFiles);

        return $operator;
    }
}
