<?php
/**
 * Candyman!
 */

namespace Mrchimp\Chimpcom\Commands;

use Chimpcom;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Candyman!
 * @action candyman
 */
class Candyman extends Command
{

  protected function configure()
  {
    $this->setName('candyman');
    $this->setDescription('Test command for action system.');
    $this->setHelp('Type "candyman" once, then optionally type it again.');
  }

  /**
   * Run the command
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    Chimpcom::setAction('candyman');
    $output->write('candyman');
  }

}
