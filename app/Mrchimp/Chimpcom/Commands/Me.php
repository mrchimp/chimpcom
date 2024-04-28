<?php

namespace Mrchimp\Chimpcom\Commands;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\ErrorCode;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class me extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('me');
        $this->setDescription('Track ME/CFS symptoms.');
        $this->addUsage('me 5 now today');
        $this->addRelated('me:view');
        $this->addArgument(
            'value',
            InputArgument::REQUIRED,
            'Amount of exertion. 0 = sleep. 5 = Extreme.'
        );
        $this->addArgument(
            'hour',
            InputArgument::OPTIONAL,
            'Hour to set value for in 24-hour time. Default: now',
        );
        $this->addArgument(
            'date',
            InputArgument::OPTIONAL,
            'Date to set value for. Default: today.'
        );
    }

    /**
     * Run the command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));

            return 1;
        }

        $value = $input->getArgument('value');
        $hour = $input->getArgument('hour');

        if (!$hour || $hour === 'now')
            $hour = date('h');
        }

        try {
            $date = $input->dateOption('date');
        } catch (InvalidFormatException $e) {
            $output->error('Invalid date.');
            return ErrorCode::INVALID_ARGUMENT;
        }

        return $this->newEntry($input, $output);
    }
}
