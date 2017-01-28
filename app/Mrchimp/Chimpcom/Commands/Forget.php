<?php
/**
 * Triggers action_forget
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Session;
use Chimpcom;
use Mrchimp\Chimpcom\Models\Memory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Triggers action_forget
 * @action forget
 */
class Forget extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('Forget');
        $this->setDescription('Deletes a memory.');
        $this->addUsage('forget 12');
        $this->addRelated('save');
        $this->addRelated('show');
        $this->addRelated('find');
        $this->addRelated('setpublic');

        $this->addArgument(
            'id',
            InputArgument::IS_ARRAY | InputArgument::REQUIRED,
            'ID of the memory to update.'
        );
    }

    /**
     * Run the command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error('You must log in to use this command.');
            return;
        }

        $user = Auth::user();
        $mem_ids = $input->getArgument('id');

        if ($mem_ids[0] == 'everything' || $mem_ids[0] == 'all') {
            $output->write('Where am I? Who are you? WHAT THE HELL\'S GOING ON?!');
            return;
        }

        $ids = Chimpcom::decodeIds($mem_ids);

        $memories = Memory::where('user_id', $user->id)
                          ->whereIn('id', $ids)
                          ->get();

        if (empty($memories)) {
            $output->error('Couldn\'t find that memory or it\'s not yours to forget.');
            return;
        }

        $output->title('Are you sure you want to forget these memories?<br>');

        $outs = [];
        foreach ($memories as $memory) {
            $outs[] = e($memory->name) . ': ' . e($memory->content);
        }

        $output->write(implode('<br>', $outs));

        Session::put('forget_id', $ids);
        Chimpcom::setAction('forget');
    }
}
