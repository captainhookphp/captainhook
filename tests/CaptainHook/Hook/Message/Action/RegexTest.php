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
use CaptainHook\App\Mockery;
use SebastianFeldmann\Git\CommitMessage;
use SebastianFeldmann\Git\Repository;
use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    use Mockery;

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
     * Tests RegexCheck::execute
     */
    public function testExecuteDefaultSuccess()
    {
        $io = $this->createPartialMock(NullIO::class, ['write']);
        $io->expects($this->once())->method('write')->with('Found matching pattern: bar');
        /** @var NullIO $io */

        $config  = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo    = new Repository($this->repo->getPath());
        $action  = new Config\Action(Regex::class, ['regex'   => '#bar#']);
        $repo->setCommitMsg(new CommitMessage('Foo bar baz'));

        $standard = new Regex();
        $standard->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
    }

    /**
     * Tests RegexCheck::execute
     */
    public function testExecuteCustomSuccess()
    {
        $successMessage = 'Regex matched';
        $io             = $this->createPartialMock(NullIO::class, ['write']);
        $io->expects($this->once())->method('write')->with($successMessage);
        /** @var NullIO $io */

        $config  = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo    = new Repository($this->repo->getPath());
        $action  = new Config\Action(
            Regex::class,
            [
                'regex'   => '#.*#',
                'success' => $successMessage
            ]
        );
        $repo->setCommitMsg(new CommitMessage('Foo bar baz'));

        $standard = new Regex();
        $standard->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
    }

    /**
     * Tests RegexCheck::execute
     */
    public function testExecuteInvalidOption()
    {
        $this->expectException(\Exception::class);

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo   = new Repository($this->repo->getPath());
        $action = new Config\Action(Regex::class);

        $standard = new Regex();
        $standard->execute($config, $io, $repo, $action);
    }


    /**
     * Tests RegexCheck::execute
     */
    public function testMerging()
    {
        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo   = $this->createRepositoryMock();
        $repo->expects($this->once())->method('isMerging')->willReturn(true);
        $action = new Config\Action(Regex::class, ['regex'   => '#.*#']);

        $standard = new Regex();
        $standard->execute($config, $io, $repo, $action);

        $this->assertTrue(true, 'Since we are merging nothing should happen');
    }

    /**
     * Tests RegexCheck::execute
     */
    public function testExecuteNoMatchCustomErrorMessage()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No match for #FooBarBaz#');

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(
            Regex::class,
            [
                'regex' => '#FooBarBaz#',
                'error' => 'No match for %s'
            ]
        );
        $repo   = new Repository($this->repo->getPath());
        $repo->setCommitMsg(new CommitMessage('Foo bar baz'));

        $standard = new Regex();
        $standard->execute($config, $io, $repo, $action);
    }
}
