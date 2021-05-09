<?php

namespace CaptainHook\App\Integration;

use CaptainHook\App\Plugin\Hook\PreserveWorkingTree;

class PreserveWorkingTreePluginTest extends IntegrationTestCase
{
    public function testMoveWorkingTreeChangesBeforeHook(): void
    {
        $repoPath = $this->setUpRepository();

        $this->setConfig($repoPath, 'plugins', [
            ['plugin' => '\\CaptainHook\\App\\Plugin\\Hook\\PreserveWorkingTree'],
            ['plugin' => '\\CaptainHook\\App\\Integration\\Plugin\\PostCheckoutEnvCheck'],
        ]);

        $this->enableHook($repoPath, 'pre-commit', [
            ['action' => 'echo "this is an action"'],
            ['action' => 'git status --porcelain=v1'],
        ]);

        $this->enableHook($repoPath, 'post-checkout');

        // Commit our changes to captainhook.json.
        $this->mustRunInShell(['git', 'commit', '-m', 'Update captainhook.json', 'captainhook.json'], $repoPath);

        // Create a file and stage it.
        $this->filesystem()->touch($repoPath . '/foo.txt');
        $this->mustRunInShell(['git', 'add', 'foo.txt'], $repoPath);

        // Make changes to the working tree that aren't staged.
        $this->filesystem()->appendToFile(
            $repoPath . '/README.md',
            "\nWorking tree changes that aren't staged.\n"
        );

        // Look at `git status` to see the changes.
        $statusResult = $this->runInShell(['git', 'status', '--porcelain=v1'], $repoPath);
        $this->assertStringContainsString(' M README.md', $statusResult->getStdout());
        $this->assertStringContainsString('A  foo.txt', $statusResult->getStdout());

        // Ensure the skip post-checkout environment variable is not set before committing.
        $envResult = $this->runInShell(['env'], $repoPath);
        $this->assertStringNotContainsString(PreserveWorkingTree::SKIP_POST_CHECKOUT_VAR, $envResult->getStdout());

        // Commit the file that's staged in the index.
        $commitResult = $this->runInShell(['git', 'commit', '-m', 'Add foo.txt'], $repoPath);

        // Output from actions appears in STDERR, so let's check it instead of STDOUT.
        // One of our actions is `git status`, so we want to assert that we do
        // not see the working tree changes listed, since they should have been
        // cached and cleared from the working tree.
        $this->assertStringContainsString('this is an action', $commitResult->getStderr());
        $this->assertStringNotContainsString(' M README.md', $commitResult->getStderr());

        // Since we have post-checkout enabled, and our pre-commit hook executes
        // `git checkout`, we want to test our post-commit hook plugin creates a
        // file with the environment variables dumped to it and that the skip
        // post-checkout env var is one of them.
        $this->assertStringContainsString(
            PreserveWorkingTree::SKIP_POST_CHECKOUT_VAR,
            file_get_contents($repoPath . '/env.txt')
        );

        // Look at `git status` again for the things we expect to see (or not).
        $statusResult = $this->runInShell(['git', 'status', '--porcelain=v1'], $repoPath);
        $this->assertStringContainsString(' M README.md', $statusResult->getStdout());
        $this->assertStringNotContainsString('A  foo.txt', $statusResult->getStdout());

        // Ensure the skip post-checkout environment variable is not set after committing.
        $envResult = $this->runInShell(['env'], $repoPath);
        $this->assertStringNotContainsString(PreserveWorkingTree::SKIP_POST_CHECKOUT_VAR, $envResult->getStdout());
    }
}
