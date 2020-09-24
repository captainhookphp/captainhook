<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\Branch\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO\NullIO;
use CaptainHook\App\Mockery;
use Exception;
use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    use Mockery;

    /**
     * Tests Regex::getRestriction
     */
    public function testConstraint(): void
    {
        $this->assertTrue(Regex::getRestriction()->isApplicableFor('pre-commit'));
        $this->assertTrue(Regex::getRestriction()->isApplicableFor('pre-push'));
        $this->assertTrue(Regex::getRestriction()->isApplicableFor('post-checkout'));
        $this->assertFalse(Regex::getRestriction()->isApplicableFor('post-commit'));
    }

    /**
     * Tests Regex::execute
     *
     * @throws \Exception
     */
    public function testExecuteDefaultSuccess(): void
    {
        $io = $this->createPartialMock(NullIO::class, ['write']);
        $io->expects($this->once())->method('write')->with('<info>OK</info> Branch name does match regex: #bar#');
        /** @var NullIO $io */

        $config  = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo    = $this->createRepositoryMock();
        $repo->expects($this->once())->method('getInfoOperator')->willReturn(
            $this->createGitInfoOperator('', 'Foo bar baz')
        );

        $action  = new Config\Action(Regex::class, ['regex' => '#bar#']);

        $standard = new Regex();
        $standard->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
    }

    /**
     * Tests Regex::execute
     *
     * @throws \Exception
     */
    public function testExecuteCustomSuccess(): void
    {
        $successMessage = 'Regex matched';
        $io             = $this->createPartialMock(NullIO::class, ['write']);
        $io->expects($this->once())->method('write')->with($successMessage);
        /** @var NullIO $io */

        $config  = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo    = $this->createRepositoryMock();
        $repo->expects($this->once())->method('getInfoOperator')->willReturn(
            $this->createGitInfoOperator('', 'Foo bar baz')
        );
        $action  = new Config\Action(
            Regex::class,
            [
                'regex'   => '#.*#',
                'success' => $successMessage
            ]
        );

        $standard = new Regex();
        $standard->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
    }

    /**
     * Tests Regex::execute
     *
     * @throws \Exception
     */
    public function testExecuteInvalidOption(): void
    {
        $this->expectException(Exception::class);

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo    = $this->createRepositoryMock();
        $action = new Config\Action(Regex::class);

        $standard = new Regex();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests Regex::execute
     *
     * @throws \Exception
     */
    public function testExecuteNoMatchDefaultErrorMessage(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('<error>FAIL</error> Branch name does not match regex: #FooBarBaz#');

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo   = $this->createRepositoryMock();
        $repo->expects($this->once())->method('getInfoOperator')->willReturn(
            $this->createGitInfoOperator('', 'Foo bar baz')
        );
        $action = new Config\Action(Regex::class, ['regex' => '#FooBarBaz#']);

        $standard = new Regex();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests Regex::execute
     *
     * @throws \Exception
     */
    public function testExecuteNoMatchCustomErrorMessage(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No match for #FooBarBaz#');

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo   = $this->createRepositoryMock();
        $repo->expects($this->once())->method('getInfoOperator')->willReturn(
            $this->createGitInfoOperator('', 'Foo bar baz')
        );
        $action = new Config\Action(
            Regex::class,
            [
                'regex' => '#FooBarBaz#',
                'error' => 'No match for %s'
            ]
        );

        $standard = new Regex();
        $standard->execute($config, $io, $repo, $action);
    }
}
