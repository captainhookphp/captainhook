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

class BeamsTest extends TestCase
{
    /**
     * @var \CaptainHook\App\Git\DummyRepo
     */
    private $repo;

    /**
     * Setup dummy repo.
     */
    public function setUp(): void
    {
        $this->repo = new DummyRepo();
        $this->repo->setup();
    }

    /**
     * Cleanup dummy repo.
     */
    public function tearDown(): void
    {
        $this->repo->cleanup();
    }

    /**
     * Tests Beams::execute
     */
    public function testExecute()
    {
        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(Beams::class);
        $repo   = new Repository($this->repo->getPath());
        $repo->setCommitMsg(new CommitMessage('Foo bar baz'));

        $standard = new Beams();
        $standard->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
    }

    /**
     * Tests Beams::execute
     */
    public function testExecuteFail()
    {
        $this->expectException(\Exception::class);

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(Beams::class);
        $repo   = new Repository($this->repo->getPath());
        $repo->setCommitMsg(new CommitMessage('added foo bar baz.'));

        $standard = new Beams();
        $standard->execute($config, $io, $repo, $action);
    }
}
