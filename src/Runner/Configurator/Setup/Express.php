<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Runner\Configurator\Setup;

use SebastianFeldmann\CaptainHook\Config;
use SebastianFeldmann\CaptainHook\Console\IOUtil;
use SebastianFeldmann\CaptainHook\Runner\Configurator\Setup;

/**
 * Class Express
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 2.2.0
 */
class Express extends Guided implements Setup
{
    /**
     * Setup hooks by asking some basic questions
     *
     * @param  \SebastianFeldmann\CaptainHook\Config $config
     * @throws \Exception
     */
    public function configureHooks(Config $config)
    {
        $msgHook = $config->getHookConfig('commit-msg');
        $preHook = $config->getHookConfig('pre-commit');
        $msgHook->setEnabled(true);
        $preHook->setEnabled(true);

        $this->setupMessageHook($msgHook);
        $this->setupPHPLintingHook($preHook);
        $this->setupPHPUnitHook($preHook);
        $this->setupPHPCodesnifferHook($preHook);
    }

    /**
     * Setup the commit message hook
     *
     * @param  \SebastianFeldmann\CaptainHook\Config\Hook $config
     * @throws \Exception
     */
    private function setupMessageHook(Config\Hook $config)
    {
        $answer = $this->io->ask(
            '  <info>Do you want to validate your commit messages?</info> <comment>[y,n]</comment> ',
            'n'
        );

        if (IOUtil::answerToBool($answer)) {
            $type    = 'php';
            $call    = '\\SebastianFeldmann\\CaptainHook\\Hook\\Message\\Action\\Beams';
            $options = ['subjectLength' => 50, 'bodyLineLength' => 72];
            $config->addAction(new Config\Action($type, $call, $options));
        }
    }

    /**
     * Setup the linting hook
     *
     * @param  \SebastianFeldmann\CaptainHook\Config\Hook $config
     * @throws \Exception
     */
    private function setupPHPLintingHook(Config\Hook $config)
    {
        $answer = $this->io->ask(
            '  <info>Do you want to check your files for syntax errors?</info> <comment>[y,n]</comment> ',
            'n'
        );

        if (IOUtil::answerToBool($answer)) {
            $type    = 'php';
            $call    = '\\SebastianFeldmann\\CaptainHook\\Hook\\PHP\\Action\\Linting';
            $config->addAction(new Config\Action($type, $call));
        }
    }

    /**
     * Setup the phpunit hook
     *
     * @param  \SebastianFeldmann\CaptainHook\Config\Hook $config
     * @throws \Exception
     */
    private function setupPHPUnitHook(Config\Hook $config)
    {
        $answer = $this->io->ask(
            '  <info>Do you want to run phpunit before committing?</info> <comment>[y,n]</comment> ',
            'n'
        );

        if (IOUtil::answerToBool($answer)) {
            $type = 'cli';
            $call = $this->io->ask(
                '  <info>Enter the phpunit command you want to execute.</info> '
              . '<comment>[phpunit]</comment> ', 'phpunit');
            $config->addAction(new Config\Action($type, $call));
        }
    }

    /**
     * Setup the code sniffer hook
     *
     * @param  \SebastianFeldmann\CaptainHook\Config\Hook $config
     * @throws \Exception
     */
    private function setupPHPCodesnifferHook(Config\Hook $config)
    {
        $answer = $this->io->ask(
            '  <info>Do you want to run phpcs before committing?</info> <comment>[y,n]</comment> ',
            'n'
        );

        if (IOUtil::answerToBool($answer)) {
            $type    = 'cli';
            $call    = $this->io->ask(
                '  <info>Enter the phpcs command you want to execute.</info> '
              . '<comment>[phpcs --standard=psr2 src]</comment> ',
                'phpcs --standard=psr2 src'
            );
            $config->addAction(new Config\Action($type, $call));
        }
    }
}
