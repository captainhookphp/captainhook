<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Runner;

use CaptainHook\App\Hook\Condition\FileChanged\Any;
use PHPUnit\Framework\TestCase;
use CaptainHook\App\Config;
use CaptainHook\App\Console\IO\Mockery as IOMockery;
use CaptainHook\App\Mockery as CHMockery;

class ConditionTest extends TestCase
{
    use IOMockery;
    use CHMockery;

    /**
     * Tests Condition::doesConditionApply
     */
    public function testDoesConditionApply(): void
    {
        $io = $this->createIOMock();
        $io->expects($this->exactly(2))->method('getArgument')->willReturn('');

        $operator   = $this->createGitDiffOperator(['fiz.php', 'baz.php', 'foo.php']);
        $repository = $this->createRepositoryMock('');
        $repository->expects($this->once())->method('getDiffOperator')->willReturn($operator);

        $conditionConfig = new Config\Condition(
            '\\' . Any::class,
            [
                ['foo.php', 'bar.php']
            ]
        );

        $runner = new Condition($io, $repository);
        $this->assertTrue($runner->doesConditionApply($conditionConfig));
    }

    /**
     * Test Condition::doesConditionApply
     */
    public function testClassNotFound(): void
    {
        $this->expectException(\Exception::class);

        $conditionConfig = new Config\Condition('\\NotFoundForSure', []);

        $runner = new Condition($this->createIOMock(), $this->createRepositoryMock());
        $runner->doesConditionApply($conditionConfig);
    }

    /**
     * Test Condition::doesConditionApply
     */
    public function testDoesConditionApplyCli(): void
    {
        if (\defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('not tested on windows');
        }

        $io         = $this->createIOMock();
        $repository = $this->createRepositoryMock('');

        $conditionConfig = new Config\Condition(CH_PATH_FILES . '/bin/phpunit');

        $runner = new Condition($io, $repository);
        $this->assertTrue($runner->doesConditionApply($conditionConfig));
    }
}
