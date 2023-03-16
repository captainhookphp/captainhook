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
use CaptainHook\App\Console\IO;
use CaptainHook\App\Exception\ActionFailed;
use CaptainHook\App\Hook\Action;
use CaptainHook\App\Hook\Input;
use CaptainHook\App\Hook\Restriction;
use CaptainHook\App\Hooks;
use SebastianFeldmann\Git\Repository;

/**
 * Class BlockFixupAndSquashCommits
 *
 * This action blocks pushes that contain fixup! or squash! commits.
 * Just as a security layer, so you are not pushing stuff you wanted to autosquash.
 *
 * Configure like this:
 *
 * {
 *    "action": "\\CaptainHook\\App\\Hook\\Branch\\Action\\BlockFixupAndSquashCommits",
 *    "options": {
 *      "blockSquashCommits": true,
 *      "blockFixupCommits": true,
 *      "protectedBranches": ["main", "master", "integration"]
 *    },
 *    "conditions": []
 *  }
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.11.0
 */
class BlockFixupAndSquashCommits implements Action
{
    /**
     * Should fixup! commits be blocked
     *
     * @var bool
     */
    private bool $blockFixupCommits = true;

    /**
     * Should squash! commits be blocked
     *
     * @var bool
     */
    private bool $blockSquashCommits = true;

    /**
     * List of protected branches
     *
     * If not specified all branches are protected
     *
     * @var array<string>
     */
    private array $protectedBranches;

    /**
     * Return hook restriction
     *
     * @return \CaptainHook\App\Hook\Restriction
     */
    public static function getRestriction(): Restriction
    {
        return Restriction::fromArray([Hooks::PRE_PUSH]);
    }

    /**
     * Execute the BlockFixupAndSquashCommits action
     *
     * @param  \CaptainHook\App\Config           $config
     * @param  \CaptainHook\App\Console\IO       $io
     * @param  \SebastianFeldmann\Git\Repository $repository
     * @param  \CaptainHook\App\Config\Action    $action
     * @return void
     * @throws \Exception
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action): void
    {
        $refDetector = new Input\PrePush();
        $refsToPush  = $refDetector->getRanges($io);

        if (empty($refsToPush)) {
            return;
        }

        $this->handleOptions($action->getOptions());

        foreach ($refsToPush as $range) {
            if (!empty($this->protectedBranches) && !in_array($range->from()->branch(), $this->protectedBranches)) {
                return;
            }
            $commits = $this->getBlockedCommits($repository, $range->from()->id(), $range->to()->id());

            if (count($commits) > 0) {
                $this->handleFailure($commits, $range->from()->branch());
            }
        }
    }

    /**
     * Check if fixup or squash should be blocked
     *
     * @param \CaptainHook\App\Config\Options $options
     * @return void
     */
    private function handleOptions(Config\Options $options): void
    {
        $this->blockSquashCommits = (bool) $options->get('blockSquashCommits', true);
        $this->blockFixupCommits  = (bool) $options->get('blockFixupCommits', true);
        $this->protectedBranches  = $options->get('protectedBranches', []);
    }

    /**
     * Returns a list of commits that should be blocked
     *
     * @param  \SebastianFeldmann\Git\Repository $repository
     * @param  string                            $remoteHash
     * @param  string                            $localHash
     * @return array<\SebastianFeldmann\Git\Log\Commit>
     * @throws \Exception
     */
    private function getBlockedCommits(Repository $repository, string $remoteHash, string $localHash): array
    {
        $typesToCheck = $this->getTypesToBlock();
        $blocked      = [];
        foreach ($repository->getLogOperator()->getCommitsBetween($remoteHash, $localHash) as $commit) {
            if ($this->hasToBeBlocked($commit->getSubject(), $typesToCheck)) {
                $blocked[] = $commit;
            }
        }
        return $blocked;
    }

    /**
     * Returns a list of strings to look for in commit messages
     *
     * Will most likely return ['fixup!', 'squash!']
     *
     * @return array<string>
     */
    private function getTypesToBlock(): array
    {
        $strings = [];
        if ($this->blockFixupCommits) {
            $strings[] = 'fixup!';
        }
        if ($this->blockSquashCommits) {
            $strings[] = 'squash!';
        }
        return $strings;
    }

    /**
     * Checks if the commit message starts with any of the given strings
     *
     * @param  string        $message
     * @param  array<string> $typesToCheck
     * @return bool
     */
    private function hasToBeBlocked(string $message, array $typesToCheck): bool
    {
        foreach ($typesToCheck as $type) {
            if (strpos($message, $type) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Generate a helpful error message and throw the exception
     *
     * @param  \SebastianFeldmann\Git\Log\Commit[] $commits
     * @param  string                              $branch
     * @return void
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    private function handleFailure(array $commits, string $branch): void
    {
        $out = [];
        foreach ($commits as $commit) {
            $out[] = ' - ' . $commit->getHash() . ' ' . $commit->getSubject();
        }
        throw new ActionFailed(
            'You are prohibited to push the following commits:' . PHP_EOL
            . ' --[ ' . $branch . ' ]-- ' . PHP_EOL
            . PHP_EOL
            . implode(PHP_EOL, $out)
        );
    }
}
