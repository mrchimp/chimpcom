<?php

/**
 * Save the description for a new project
 */

namespace Mrchimp\Chimpcom\Actions;

use Mrchimp\Chimpcom\Actions\Action;
use App\Mrchimp\Chimpcom\Id;
use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Booleanate;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Models\Memory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Save the description for a new project
 * @action normal
 */
class Forget extends Action
{

    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('forget');

        $this->addArgument(
            'answer',
            InputArgument::REQUIRED,
            'Yes/no confirmation.'
        );
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error('You must log in to use this command.');
            $output->resetTerminal();

            return 1;
        }

        $user = Auth::user();
        $answer = $input->getArgument('answer');

        if (Booleanate::isAffirmative($answer)) {
            $ids = $input->getActionData('forget_id');

            Memory::where('user_id', $user->id)
                ->whereIn('id', $ids)
                ->delete();

            $ids = Id::encodeMany($ids);

            $output->alert((count($ids) > 1 ? 'Memories' : 'Memory') . ' forgotten: #' . implode(', #', $ids));
        } elseif (Booleanate::isNegative($answer)) {
            $output->write('Action aborted.');
        } else {
            $output->write('Whatever.');
        }

        Chimpcom::delAction($input->getActionId());

        return 0;
    }
}
