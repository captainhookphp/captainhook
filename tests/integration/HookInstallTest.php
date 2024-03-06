<?php

namespace CaptainHook\App\Integration;

class HookInstallTest extends IntegrationTestCase
{
    public function testGitHooksSuccessfullyInstallWithComposerUpdate(): void
    {
        $repoPath = $this->setUpRepository(false);

        $result = $this->runInShell(['composer', 'update', '--no-ansi', '--no-interaction'], $repoPath);

        $this->assertStringContainsString("commit-msg installed", $result->getStdout());
        $this->assertStringContainsString("pre-push installed", $result->getStdout());
        $this->assertStringContainsString("pre-commit installed", $result->getStdout());
        $this->assertStringContainsString("prepare-commit-msg installed", $result->getStdout());
        $this->assertStringContainsString("post-commit installed", $result->getStdout());
        $this->assertStringContainsString("post-merge installed", $result->getStdout());
        $this->assertStringContainsString("post-checkout installed", $result->getStdout());
        $this->assertStringContainsString("post-rewrite installed", $result->getStdout());
    }
}
