<?php
/**
 * Convert decimal to hexadecimal
 */

namespace Mrchimp\Chimpcom\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Convert decimal to hexadecimal
 */
class Dechex extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('dechex');
        $this->setDescription('Convert decimal to hexadecimal.');
        $this->setHelp(<<<EOT
Values can be plain decimals or rgb values (e.g. rgb(0,128,255)) as long as the
values do not include spaces.
EOT
        );

        $this->addArgument(
            'input',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Decimal numbers to convert.'
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

        foreach ($values as &$value) {
            if (substr($value, 0, 4) === 'rgb(') {
                $value = substr($value, 4, -1);
                $chunks = explode(',', $value);
                foreach ($chunks as &$chunk) {
                    $chunk = dechex($chunk);
                }
                $value = '#' . implode('', $chunks);
                $value .= ' <span style="color:' . $value . '">███████</span>';
            } else {
                $value = strval(dechex((int)$value));
            }
        }

        $output->write(implode('<br>', $values));
    }

}
