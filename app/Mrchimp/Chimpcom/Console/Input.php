<?php

namespace Mrchimp\Chimpcom\Console;

use Chimpcom;
use Carbon\Carbon;
use Mrchimp\Chimpcom\Str;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Input\StringInput;

class Input extends StringInput
{
    protected $content;

    protected $action_id;

    protected $action;

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setActionId(string $action_id = null): void
    {
        $this->action_id = $action_id;

        if ($action_id) {
            $this->action = Chimpcom::getAction($this->action_id);
        }
    }

    public function getAction(): ?array
    {
        return $this->action;
    }

    public function getActionData(string $key): null | string | array
    {
        if (!$this->action) {
            return null;
        }

        return Arr::get($this->action, 'data.' . $key);
    }

    public function getActionId(): ?string
    {
        return $this->action_id;
    }

    /**
     * Take a string or an array of words and split it into an array of
     * words and an array of tags
     */
    public function splitWordsAndTags($input = []): array
    {
        return Str::splitWordsAndTags($input);
    }

    public function dateOption($option, $default = 'now'): Carbon
    {
        $date_str = $this->getOption($option);

        if ($date_str) {
            return Carbon::parse($date_str, 'UTC');
        } else {
            return new Carbon();
        }
    }
}
