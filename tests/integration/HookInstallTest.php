<?php

namespace CaptainHook\App\Integration;

class HookInstallTest extends IntegrationTestCase
{
    public function testGitHooksSuccessfullyInstallWithComposerUpdate(): void
    {
        $repoPath = $this->setUpRepository(false);

        $result = $this->runInShell(['composer', 'update', '--no-ansi', '--no-interaction'], $repoPath);

        $this->assertStringContainsString("'commit-msg' hook installed successfully", $result->getStdout());
        $this->assertStringContainsString("'pre-push' hook installed successfully", $result->getStdout());
        $this->assertStringContainsString("'pre-commit' hook installed successfully", $result->getStdout());
        $this->assertStringContainsString("'prepare-commit-msg' hook installed successfully", $result->getStdout());
        $this->assertStringContainsString("'post-commit' hook installed successfully", $result->getStdout());
        $this->assertStringContainsString("'post-merge' hook installed successfully", $result->getStdout());
        $this->assertStringContainsString("'post-checkout' hook installed successfully", $result->getStdout());
        $this->assertStringContainsString("'post-rewrite' hook installed successfully", $result->getStdout());
        $this->assertStringContainsString("'post-change' hook installed successfully", $result->getStdout());
    }
}
