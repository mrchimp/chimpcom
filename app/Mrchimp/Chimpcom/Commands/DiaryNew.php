<?php

namespace Mrchimp\Chimpcom\Commands;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\ErrorCode;
use Mrchimp\Chimpcom\Traits\ManagesProjects;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DiaryNew extends Command
{
    use ManagesProjects;

    const DEFAULT_NUM = 10;

    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('diary:new');
        $this->setDescription('Create diary entries.');
        $this->addUsage('diary:new Here is an entry with a @tag in it --project=myproject --date=yesterday');
        $this->addRelated('diary');
        $this->addRelated('diary:read');
        $this->addRelated('diary:edit');
        $this->addRelated('project');
        $this->addRelated('tag');
        $this->addArgument(
            'content',
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'The diary entry.'
        );
        $this->addOption(
            'project',
            'p',
            InputOption::VALUE_REQUIRED,
            'The project to attach the entry to.'
        );
        $this->addOption(
            'date',
            'd',
            InputOption::VALUE_REQUIRED,
            'The date that the entry is for.'
        );
        $this->addOption(
            'meta',
            'm',
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Add meta data to the entry. E.g. --meta=foo:bar'
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

        return $this->newEntry($input, $output);
    }

    /**
     * Create an entry
     */
    protected function newEntry(InputInterface $input, OutputInterface $output): int
    {
        try {
            $date = $input->dateOption('date');
        } catch (InvalidFormatException $e) {
            $output->error('Invalid date.');
            return ErrorCode::INVALID_ARGUMENT;
        }

        $project_name = $input->getOption('project');
        $project = $this->projectFromName($project_name);
        $meta = $this->parseMeta($input->getOption('meta'));

        if ($project_name && !$project) {
            $output->error('Project not found.');
            return ErrorCode::MODEL_NOT_FOUND;
        }

        [$words, $tags] = $input->splitWordsAndTags($input->getArgument('content'));
        $content = implode(' ', $words);

        if (empty($words)) {
            $output->error("You didn't enter any content.");
            return ErrorCode::INVALID_ARGUMENT;
        }

        $existing_entry = Auth::user()->diaryEntries()->whereDate('date', '=', $date)->first();

        if ($existing_entry) {
            $output->setAction('diary_edit', [
                'entry_id' => $existing_entry->id,
            ]);
            $output->editContent($existing_entry->content . "\n\n" . $content);
            return ErrorCode::OK;
        }

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

    protected function parseMeta($meta = [])
    {
        return array_reduce($meta, function ($carry, $item) {
            $parts = explode(':', $item, 2);

            if (count($parts) < 2) {
                return $carry;
            }

            $carry[$parts[0]] = $parts[1];

            return $carry;
        }, []);
    }
}
