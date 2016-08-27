<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Console\IO;

use Symfony\Component\Console\Output\OutputInterface;

class DefaultIOTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Symfony\Component\Console\Input\InputInterface
     */
    public function getInputMock()
    {
        return $this->getMockBuilder('\\Symfony\\Component\\Console\\Input\\InputInterface')
                     ->disableOriginalConstructor()
                     ->getMock();
    }

    /**
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutputMock()
    {
        return $this->getMockBuilder('\\Symfony\\Component\\Console\\Output\\OutputInterface')
                     ->disableOriginalConstructor()
                     ->getMock();
    }

    /**
     * @return \Symfony\Component\Console\Helper\HelperSet
     */
    public function getHelperSetMock()
    {
        return $this->getMockBuilder('\\Symfony\\Component\\Console\\Helper\\HelperSet')
                     ->disableOriginalConstructor()
                     ->getMock();
    }

    /**
     * @return \Symfony\Component\Console\Helper\QuestionHelper
     */
    public function getQuestionHelper()
    {
        return $this->getMockBuilder('\\Symfony\\Component\\Console\\Helper\\QuestionHelper')
                     ->disableOriginalConstructor()
                     ->getMock();
    }

    /**
     * Tests DefaultIO::isInteractive
     */
    public function testIsInteractive()
    {
        $input  = $this->getInputMock();
        $output = $this->getOutputMock();
        $helper = $this->getHelperSetMock();

        $input->expects($this->once())->method('isInteractive')->willReturn(false);
        $io = new DefaultIO($input, $output, $helper);

        $this->assertFalse($io->isInteractive());
    }

    /**
     * Tests DefaultIO::isVerbose
     */
    public function testIsVerbose()
    {
        $input  = $this->getInputMock();
        $output = $this->getOutputMock();
        $helper = $this->getHelperSetMock();

        $output->expects($this->once())->method('getVerbosity')->willReturn(0);
        $io = new DefaultIO($input, $output, $helper);

        $this->assertFalse($io->isVerbose());
    }

    /**
     * Tests DefaultIO::isVeryVerbose
     */
    public function testIsVeryVerbose()
    {
        $input  = $this->getInputMock();
        $output = $this->getOutputMock();
        $helper = $this->getHelperSetMock();

        $output->expects($this->once())->method('getVerbosity')->willReturn(0);
        $io = new DefaultIO($input, $output, $helper);

        $this->assertFalse($io->isVeryVerbose());
    }

    /**
     * Tests DefaultIO::isDebug
     */
    public function testIsDebug()
    {
        $input  = $this->getInputMock();
        $output = $this->getOutputMock();
        $helper = $this->getHelperSetMock();

        $output->expects($this->once())->method('getVerbosity')->willReturn(0);
        $io = new DefaultIO($input, $output, $helper);

        $this->assertFalse($io->isDebug());
    }

    /**
     * Tests DefaultIO::writeError
     */
    public function testWriteError()
    {
        $input  = $this->getInputMock();
        $output = $this->getOutputMock();
        $helper = $this->getHelperSetMock();

        $output->expects($this->once())->method('getVerbosity')->willReturn(OutputInterface::VERBOSITY_DEBUG);
        $io = new DefaultIO($input, $output, $helper);

        $io->writeError('foo');
    }

    /**
     * Tests DefaultIO::ask
     */
    public function testAsk()
    {
        $input          = $this->getInputMock();
        $output         = $this->getOutputMock();
        $helper         = $this->getHelperSetMock();
        $questionHelper = $this->getQuestionHelper();

        $helper->expects($this->once())->method('get')->willReturn($questionHelper);
        $questionHelper->expects($this->once())->method('ask')->willReturn(true);

        $io     = new DefaultIO($input, $output, $helper);
        $answer = $io->ask('foo');
        $this->assertTrue($answer);
    }

    /**
     * Tests DefaultIO::askConfirmation
     */
    public function testAskConfirmation()
    {
        $input          = $this->getInputMock();
        $output         = $this->getOutputMock();
        $helper         = $this->getHelperSetMock();
        $questionHelper = $this->getQuestionHelper();

        $helper->expects($this->once())->method('get')->willReturn($questionHelper);
        $questionHelper->expects($this->once())->method('ask')->willReturn(true);

        $io     = new DefaultIO($input, $output, $helper);
        $answer = $io->askConfirmation('foo');
        $this->assertTrue($answer);
    }

    /**
     * Tests DefaultIO::askAbdValidate
     */
    public function testAskAndValidate()
    {
        $input          = $this->getInputMock();
        $output         = $this->getOutputMock();
        $helper         = $this->getHelperSetMock();
        $questionHelper = $this->getQuestionHelper();

        $helper->expects($this->once())->method('get')->willReturn($questionHelper);
        $questionHelper->expects($this->once())->method('ask')->willReturn(true);

        $io     = new DefaultIO($input, $output, $helper);
        $answer = $io->askAndValidate('foo', function() { return true; });
        $this->assertTrue($answer);
    }
}
