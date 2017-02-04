<?php
/**
 * Candyman!
 */

namespace Mrchimp\Chimpcom\Actions;

use Auth;
use Chimpcom;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Commands\Command;
use Symfony\Component\Console\Input\InputArgument;
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
    $this->setDescription('Maybe say something scary.');
    $this->addArgument('response', InputArgument::OPTIONAL, 'User\'s response.');
  }

  /**
   * Run the command
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    if ($input->getArgument('response') === 'candyman'){
      $output->error('AAAAAAAAGH!');
    } else {
      $output->write('Pussy.');
    }

    Chimpcom::setAction('normal');
  }
}