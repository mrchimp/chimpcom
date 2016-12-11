<?php
/**
 * Convert hexadecimal to decimal
 */

namespace Mrchimp\Chimpcom\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Convert hexadecimal to decimal
 */
class Hexdec extends Command
{

    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('hexdec');
        $this->setDescription('Convert hexadecimal to decimal.');

        $this->addArgument(
            'input',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Hexadecimal numbers to convert.'
        );
    }

    /**
     * Run the command
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $values = $input->getArgument('input');
        $output_vals = [];

        foreach ($values as &$value){
            $output_val = '';
            // Check for colour values
            if (substr($value, 0, 1) === '#') {
                $value = substr($value, 1);
                $length = strlen($value);

                if ($length == 3) { // FFF
                    $parts = str_split($value, 1);
                    $output_val = 'rgb(' . hexdec($parts[0] . $parts[0]) . ', ' .
                    hexdec($parts[1] . $parts[1]) . ', ' .
                    hexdec($parts[2] . $parts[2]) . ') ';
                    $output_val .= '<span style="color:#'.$value.'">███████</span>';
                } else if ($length == 6) { #FFFFFF
                    $parts = str_split($value, 2);
                    $output_val = 'rgb(' . hexdec($parts[0]) . ', ' .
                    hexdec($parts[1]) . ', ' .
                    hexdec($parts[2]) . ') ';
                    $output_val .= '<span style="color:#'.$value.'">███████</span>';
                } else {
                    $output->error('I don\'t know how to handle this.');
                    return;
                }
            } else {
                $output_val = hexdec($value);
            }

            $output_vals[] = $output_val;
        }

        $output->write(implode('<br>', $output_vals));
        return true;
    }
}
