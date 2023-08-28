<?php

namespace Mrchimp\Chimpcom\Commands;

use App\Mrchimp\Chimpcom\Id;
use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\ErrorCode;
use Mrchimp\Chimpcom\Facades\Format;
use Mrchimp\Chimpcom\Models\Memory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Triggers action_forget
 * @action forget
 */
class NoteForget extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('note:forget');
        $this->setDescription('Deletes a memory.');
        $this->addUsage('forget 12');
        $this->addRelated('note:new');
        $this->addRelated('note:show');
        $this->addRelated('note:find');
        $this->addRelated('note:setpublic');

        $this->addArgument(
            'id',
            InputArgument::IS_ARRAY | InputArgument::REQUIRED,
            'ID of the memory to update.'
        );
    }

    /**
     * Run the command
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));
            return ErrorCode::NOT_AUTHORISED;
        }

        $user = Auth::user();
        $mem_ids = $input->getArgument('id');

        if ($mem_ids[0] == 'everything' || $mem_ids[0] == 'all') {
            $output->write(Format::escape('Where am I? Who are you? WHAT THE HELL\'S GOING ON?!'));
            return ErrorCode::SUCCESS;
        }

        $ids = Id::decodeMany($mem_ids);

        $memories = Memory::where('user_id', $user->id)
            ->whereIn('id', $ids)
            ->get();

        if ($memories->isEmpty()) {
            $output->error(Format::escape('Couldn\'t find that memory or it\'s not yours to forget.'));
            return ErrorCode::MODEL_NOT_FOUND;
        }

        $output->title('Are you sure you want to forget these memories?' . Format::nl());

        $outs = [];
        foreach ($memories as $memory) {
            $outs[] = Format::escape($memory->name) . ': ' . Format::escape($memory->content);
        }

        $output->write(implode(Format::nl(), $outs));

        $output->setAction('forget', [
            'forget_id' => $ids,
        ]);
        $output->useQuestionInput();

        return ErrorCode::SUCCESS;
    }
}
