<?php

/**
 * Chimpcom command input
 */

namespace Mrchimp\Chimpcom;

use Mrchimp\Chimpcom\Models\Alias as ChimpcomAlias;
use Mrchimp\Chimpcom\Console\Command;

use Symfony\Component\Console\Output\BufferedOutput as Output;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Chimpcom command input
 */
class Input
{

    /**
     * The whole input string as provided by the user
     * @var string
     */
    private $cmd_in;

    /**
     * Class constructor
     * @param string $cmd_in Command as input by user
     * @param OptionCollection $specs Command line parser spec
     */
    public function __construct($cmd_in, $specs = null) {
        // $hi = new Mrchimp\Chimpcom\Commands\Hi();
        // $command = new Command('test');
        // $command->setCode(Mrchimp\Chimpcom\Commands\Hi);
        // $command->addArgument('last_name', InputArgument::OPTIONAL, 'Your last name?');
        // $input = new StringInput($cmd_in);
        // $output = new Output();
        // $command->run($input, $output);
        // dd($input, $output->fetch());
        // $specs = new OptionCollection;
    }
}
