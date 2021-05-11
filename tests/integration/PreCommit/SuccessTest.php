<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Integration\PreCommit;

use CaptainHook\App\Integration\IntegrationTestCase;

class SuccessTest extends IntegrationTestCase
{
    public function testPreCommitSuccess(): void
    {
        $repoPath = $this->setUpRepository();

        $this->enableHook($repoPath, 'pre-commit', [
            ['action' => 'echo "test action placeholder {$STAGED_FILES|of-type:html}" > html.log']
        ]);

        $this->filesystem()->touch($repoPath . '/foo.html');
        $this->mustRunInShell(['git', 'add', 'foo.html'], $repoPath);
        $this->mustRunInShell(['git', 'commit', '-m', 'Test successful pre-commit execution'], $repoPath);

        $log = file_get_contents($repoPath . '/html.log');

        $this->assertStringContainsString("foo.html", $log);
    }
}
