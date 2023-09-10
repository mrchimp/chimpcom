<?php

namespace Mrchimp\Chimpcom\Traits;

use Auth;
use App\Mrchimp\Chimpcom\Id;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Mrchimp\Chimpcom\ErrorCode;
use Mrchimp\Chimpcom\Facades\Format;
use Mrchimp\Chimpcom\Models\Tag;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait ManagesTags
{
    protected function manageTags(InputInterface $input, OutputInterface $output)
    {
        $user = Auth::user();
        $project = $user->activeProject;
        $remove = $input->getOption('remove');

        if (!$project) {
            $output->error('No active project. Use `PROJECT LIST` and `PROJECT SET x`.');
            return ErrorCode::NO_ACTIVE_PROJECT;
        }

        $content = $input->getArgument('content');
        [$words, $tags] = $input->splitWordsAndTags($content);

        if (empty($words)) {
            $output->error('No IDs provided.');
            return ErrorCode::INVALID_ARGUMENT;
        }

        if (empty($tags)) {
            $output->error('No tags provided.');
            return ErrorCode::INVALID_ARGUMENT;
        }

        $ids = array_map(fn ($word) => Id::decode($word), $words);

        $items = $this->findItems($ids, $input, $output);

        if (empty($items)) {
            $output->error('No items found with the given IDs.');
            return ErrorCode::MODEL_NOT_FOUND;
        }

        $output->write(($remove ? 'Removing' : 'Adding') . ' tags: ' . implode(', ', $tags) . Format::nl());

        $tag_models = new Collection();

        foreach ($tags as $tag_name) {
            $tag_models->push(Tag::firstOrCreate([
                'tag' => $tag_name,
            ]));
        }

        $tag_ids = $tag_models->pluck('id');

        if ($remove) {
            $items->each(fn ($item) => $item->tags()->detach($tag_ids));
        } else {
            $items->each(fn ($item) => $item->tags()->syncWithoutDetaching($tag_ids));
        }

        $output->alert('Ok.');

        return ErrorCode::OK;
    }

    abstract protected function findItems($ids = [], InputInterface $input): EloquentCollection;
}
