<?php

namespace Mrchimp\Chimpcom\Actions;

use Auth;
use Mrchimp\Chimpcom\ErrorCode;
use Mrchimp\Chimpcom\Traits\HandlesMetadata;
use Mrchimp\Chimpcom\Traits\ManagesProjects;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DiaryNew extends Action
{
    use ManagesProjects, HandlesMetadata;

    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('diary_new');
        $this->setDescription('Handle new diary content and save it');
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = $input->getActionData('date');
        $meta = $this->parseMeta($input->getActionData('meta'));
        $project = $this->projectFromName($input->getActionData('project_name'));

        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));
            return ErrorCode::NOT_AUTHORISED;
        }

        if (!$date) {
            $output->error('No date specified.');
            return ErrorCode::INVALID_ARGUMENT;
        }

        $raw_content = $input->getContent();
        [$words, $tags] = $input->splitWordsAndTags($raw_content);
        $content = implode(' ', $words);

        $entry = Auth::user()->diaryEntries()->create([
            'content' => $content,
            'project_id' => $project ? $project->id : null,
            'date' => $date,
            'meta' => $meta,
        ]);

        $entry->attachTags($tags);

        $output->alert('Diary entry saved.');

        return ErrorCode::OK;
    }
}
