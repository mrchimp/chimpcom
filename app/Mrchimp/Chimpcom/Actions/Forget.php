<?php
/**
 * Save the description for a new project
 */

namespace Mrchimp\Chimpcom\Actions;

use Auth;
use Session;
use Chimpcom;
use App\User;
use Illuminate\Http\Request;
use Mrchimp\Chimpcom\Booleanate;
use Mrchimp\Chimpcom\Models\Memory;
use Mrchimp\Chimpcom\Commands\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Save the description for a new project
 * @action normal
 */
class Forget extends Command
{

    protected $log_this = false;

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
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error('You must log in to use this command.');
            Chimpcom::resetTerminal();
            return;
        }

        $user = Auth::user();
        $answer = $input->getArgument('answer');

        if (Booleanate::isAffirmative($answer)) {
          $ids = Session::get('forget_id');

          $memories = Memory::where('user_id', $user->id)
                            ->whereIn('id', $ids)
                            ->delete();

          $ids = Chimpcom::encodeIds($ids);
          $output->alert((count($ids) > 0 ? 'Memories' : 'memory') .' forgotten: #' . implode(', #', $ids));
        } else if (Booleanate::isNegative($answer)) {
          $output->write('Action aborted.');
        } else {
          $output->write('Whatever.');
        }

        Chimpcom::setAction('normal');
        Session::forget('forget_id');
    }

}
