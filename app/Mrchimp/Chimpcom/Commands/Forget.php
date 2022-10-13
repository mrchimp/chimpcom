<?php

namespace Mrchimp\Chimpcom\Commands;

use App\Mrchimp\Chimpcom\Id;
use Chimpcom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Mrchimp\Chimpcom\Facades\Format;
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
            $output->error(__('chimpcom.must_log_in'));
            return 1;
        }

        $user = Auth::user();
        $mem_ids = $input->getArgument('id');

        if ($mem_ids[0] == 'everything' || $mem_ids[0] == 'all') {
            $output->write(Format::escape('Where am I? Who are you? WHAT THE HELL\'S GOING ON?!'));
            return 2;
        }

        $ids = Id::decodeMany($mem_ids);

        $memories = Memory::where('user_id', $user->id)
                        ->whereIn('id', $ids)
                        ->get();

        if ($memories->isEmpty()) {
            $output->error(Format::escape('Couldn\'t find that memory or it\'s not yours to forget.'));
            return 3;
        }

        $output->title('Are you sure you want to forget these memories?' . Format::nl());

        $outs = [];
        foreach ($memories as $memory) {
            $outs[] = Format::escape($memory->name) . ': ' . Format::escape($memory->content);
        }

        $output->write(implode(Format::nl(), $outs));

        $output->useQuestionInput();
        Session::put('forget_id', $ids);
        Chimpcom::setAction('forget');

        return 0;
    }
}
