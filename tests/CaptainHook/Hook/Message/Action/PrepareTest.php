<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook\Message\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO\NullIO;
use CaptainHook\App\Git\DummyRepo;
use SebastianFeldmann\Git\CommitMessage;
use SebastianFeldmann\Git\Repository;
use PHPUnit\Framework\TestCase;

class PrepareTest extends TestCase
{
    /**
     * @var \CaptainHook\App\Git\DummyRepo
     */
    private $repo;

    /**
     * Setup dummy repo.
     */
    public function setUp()
    {
        $this->repo = new DummyRepo();
        $this->repo->setup();
    }

    /**
     * Cleanup dummy repo.
     */
    public function tearDown()
    {
        $this->repo->cleanup();
    }

    /**
     * Tests RegexCheck::execute
     */
    public function testExecutePrepareMessage()
    {
        /** @var NullIO $io */
        $io      = $this->createPartialMock(NullIO::class, ['write']);
        $config  = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo    = new Repository($this->repo->getPath());
        $action  = new Config\Action(
            'php',
            Prepare::class,
            [
                'message' => 'Prepared Commit Message'
            ]
        );
        $repo->setCommitMsg(new CommitMessage('Foo bar baz'));

        $standard = new Prepare();
        $standard->execute($config, $io, $repo, $action);

        $this->assertEquals('Prepared Commit Message', $repo->getCommitMsg()->getRawContent());
    }
}
