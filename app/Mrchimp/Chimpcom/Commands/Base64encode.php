<?php
/**
 * Encode a string in base64
 */

namespace Mrchimp\Chimpcom\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Encode a string in base64
 */
class Base64encode extends Command
{
  /**
   * Configure the command
   *
   * @return void
   */
  protected function configure()
  {
      $this->setName('base64encode');
      $this->setDescription('Encodes a base64 encoded string.');
      $this->addArgument(
          'input',
          InputArgument::REQUIRED,
          'A plaintext string to be encoded.'
      );
  }

  /**
   * Run the command
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $encoded = $input->getArgument('input');
    $decoded = base64_encode($encoded);
    $output->write(e($decoded));
  }

}
