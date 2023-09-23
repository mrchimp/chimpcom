<?php

namespace Mrchimp\Chimpcom;

use Illuminate\Support\Str;
use App\Mrchimp\Chimpcom\Id;
use Mrchimp\Chimpcom\Models\Feed;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Models\DiaryEntry;

/**
 * Wrap strings in spans
 */
class FormatHtml implements Format
{
    /**
     * Wrap text in a span with a class and attributes.
     */
    public static function style($str, $class, array $attr = []): string
    {
        return '<span class="' . $class . '"' . static::attrsToString($attr) . '>' . $str . '</span>';
    }

    /**
     * Format a string as an error
     */
    public static function error($str, array $attr = []): string
    {
        return self::style($str, 'red_highlight', $attr);
    }

    /**
     * Format a string as faded text
     */
    public static function grey($str, array $attr = []): string
    {
        return self::style($str, 'grey_text', $attr);
    }

    /**
     * Format a string as an alert
     */
    public static function alert($str, array $attr = []): string
    {
        return self::style($str, 'green_highlight', $attr);
    }

    /**
     * Format a string as a title
     */
    public static function title($str, array $attr = []): string
    {
        return self::style($str, 'blue_highlight', $attr);
    }

    /**
     * Wrap a string in a link
     */
    public static function link($str, $url, array $attr = []): string
    {
        return '<a href="' . $url . '" ' . static::attrsToString($attr) . '>' . $str . '</a>';
    }

    /**
     * Format text that should look like code.
     */
    public static function code($str, array $attr = []): string
    {
        return '<code ' . static::attrsToString($attr) . '>' . $str . '</code>';
    }

    /**
     * Format text that should look like bold.
     */
    public static function bold($str, array $attr = []): string
    {
        return '<strong>' . $str . '</strong>';
    }

    /**
     * Converts an dimensional array into an html table
     */
    public static function listToTable(array $list, int $cols = 1, bool $sort_list = false, $titles = []): string
    {
        $cols = ($cols < 1 ? 1 : $cols);

        if ($sort_list) {
            sort($list);
        }

        $num_of_items = count($list);
        $output_count = 0;
        $row_count = 1;
        $s = '<table>';

        if (!empty($titles)) {
            $s .= '<tr>';
            foreach ($titles as $title) {
                $s .= '<th>' . e($title) . '</th>';
            }
            $s .= '</tr>';
        }

        while (isset($list[$output_count])) {
            $s .= ($row_count == 1 ? '<tr>' : '');
            $s .= '<td>' . $list[$output_count] . '</td>';

            if ($row_count === $cols || $output_count + 1 === $num_of_items) {
                $s .= '</tr>';
            }

            if ($output_count === $num_of_items) {
                break;
            }

            $row_count = ($row_count % $cols) + 1;
            $output_count++;
        }

        $s .= '</table>';
        return $s;
    }

    /**
     * Output memories
     */
    public static function memories(Collection $memories): string
    {
        $previous_name = '';
        $chunks = [];
        $output = '';
        $current_user = Auth::user();

        foreach ($memories as $memory) {
            if ($memory->name != $previous_name) {
                $output .= static::listToTable($chunks, 6) . '<br>';
                $output .= static::alert(e(ucwords($memory->name))) . '<br>';
                $chunks = [];
            }

            $hexid = Id::encode($memory['id']);

            // Memory ID
            $chunks[] = static::grey($hexid, [
                'data-type' => 'autofill',
                'data-autofill' => e("forget $hexid")
            ]);

            if ($memory->isMine()) {
                $chunks[] = static::title(e($memory->user->name));
            } else {
                $chunks[] = static::grey(e($memory->user->name));
            }

            // Public
            if ($memory->public) {
                $chunks[] = static::alert('P', [
                    'title' => 'Public: anyone can see this.',
                    'data-type' => 'autofill',
                    'data-autofill' => e("setpublic $hexid --private")
                ]);
            } else {
                $chunks[] = static::grey('p', [
                    'title' => 'Private: only you can see this',
                    'data-type' => 'autofill',
                    'data-autofill' => e("setpublic $hexid")
                ]);
            }

            // Major
            $pos = strpos($memory->content, '#major');
            if ($pos !== false) {
                $chunks[] = static::error('!', [
                    'title' => 'Major! This is important! Take notice! Act now!'
                ]);
            } else {
                $chunks[] = '<span>' . static::nbsp() . '</span>';
            }

            // Minor
            $pos = strpos($memory->content, '#minor');
            $minor = ($pos !== false);

            // Content
            if (Auth::check() && $current_user->id == $memory->user_id) {
                $attrs = [
                    'data-type' => 'autofill',
                    'data-autofill' => e("update $hexid {$memory['content']}")
                ];
            } else {
                $attrs = [];
            }

            if ($minor) {
                $chunks[] = static::grey(static::autoLink($memory->content), $attrs);
            } else {
                $chunks[] = static::style(static::autoLink($memory->content), '', $attrs);
            }

            if ($memory->tags->isNotEmpty()) {
                $chunks[] = static::grey('@' . implode(', @', $memory->tags->pluck('tag')->toArray()));
            } else {
                $chunks[] = '';
            }

            $previous_name = $memory->name;
        }

        $output .= static::listToTable($chunks, 6) . '<br>';

        if (count($memories) > 5) {
            $output .= '<br>' . count($memories) . ' memories found.';
        }

        return $output;
    }

    /**
     * Replace URLs in text with html links.
     */
    public static function autoLink(string $input): string
    {
        $input = e($input);
        $pattern  = '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#';

        $callback = function ($matches) {
            $url       = array_shift($matches);
            $url_parts = parse_url($url);

            $text = preg_replace("/^www./", "", $url_parts["host"]) . (isset($url_parts["path"]) ? "/..." : "");

            return sprintf('<a target="_blank" rel="nofollow noreferrer" href="%s">%s</a>', $url, $text);
        };

        return preg_replace_callback($pattern, $callback, $input);
    }

    /**
     * Format task lists
     */
    public static function tasks(Collection $tasks, $show_dates = false, $show_project = false): string
    {
        $cols = 5;
        $list = [];
        $titles = ['ID', 'Priority', 'Description', 'Tags', 'Completed'];

        $output = '';

        if ($show_dates) {
            $cols++;
            $titles[] = 'Created';
        }

        if ($show_project) {
            $cols++;
            $titles[] = 'Project';
        }

        foreach ($tasks as $task) {
            $hex_id = Id::encode($task->id);

            $list[] = static::style(
                $hex_id,
                'autofill',
                [
                    'data-type' => 'autofill',
                    'data-autofill' => 'task:done ' . $hex_id
                ]
            );

            if ($task->priority > 10) {
                $class = 'task-urgent';
            } elseif ($task->priority > 5) {
                $class = 'task-high';
            } elseif ($task->priority < 1) {
                $class = 'task-low';
            } else {
                $class = 'task-normal';
            }

            $list[] = static::style(
                $task->priority,
                $class . ' autofill',
                [
                    'data-type' => 'autofill',
                    'data-autofill' => 'task:edit ' . $hex_id . ' --priority ' . $task->priority
                ]
            );

            $list[] = static::autoLink($task->description);

            $list[] = static::grey($task->tags->map(function ($tag) {
                return '@' . e($tag->tag);
            })->implode(", "));

            if ($task->completed) {
                $list[] = static::grey($task->time_completed);
            } else {
                $list[] = '';
            }

            if ($show_dates) {
                $list[] = static::grey($task->created_at);
            }

            if ($show_project) {
                $list[] = static::grey($task->project->name);
            }
        }

        $output .= static::listToTable(
            $list,
            $cols,
            false,
            $titles
        );

        return $output;
    }

    public static function events(Collection $events): string
    {
        $output = '';

        foreach ($events as $event) {
            $output .= static::title($event->date->toDayDateTimeString()) . static::nl();
            $output .= e($event->description);

            if ($event->tags->isNotEmpty()) {
                $output .= static::grey(static::nl() . 'Tags: ' . $event->tags->map(function ($tag) {
                    return e($tag->tag);
                })->join(', '));
            }
        }

        return $output;
    }

    /**
     * Format PMs
     */
    public static function messages(Collection $messages): string
    {
        $output = '<table>
                <tr>
                    <td>' . static::title('id') . '</td>
                    <td>' . static::title('From') . '</td>
                    <td colspan="2">' . static::title('Message') . '</td>
                </tr>';

        foreach ($messages as $msg) {
            $output .= '<tr>' .
                '<td>' . $msg->id . '</td>' .
                '<td>' . e($msg->author ? $msg->author->name : 'Unknown user') . '</td>' .
                '<td>' . e($msg->message) . '</td>' .
                '<td>' . ($msg->has_been_read ? '&nbsp;' : static::alert('New')) . '</td>' .
                '</tr>';
        }

        $output .= '</table>';

        return $output;
    }

    /**
     * Format a Feed as a string
     */
    public static function feed(Feed $feed): string
    {
        $output = '';
        $output .= self::alert('<br>' . $feed->get_title()) . '<br><br>';
        $item_count = $feed->get_item_quantity();

        for ($i = 0; $i < $item_count; $i++) {
            $item = $feed->get_item($i);
            $output .= self::feedItem($item);
        }

        return $output;
    }

    /**
     * Format a single feed item
     */
    public static function feedItem($item): string
    {
        $output = self::title(self::escape($item->get_title())) . '<br>';
        $author = $item->get_author();

        if ($author) {
            $output .= 'Author: ' . self::escape($author->get_name());
        }

        $output .= self::grey(self::escape($item->get_date('Y-m-d H:i:s'))) . '<br>';
        $output .= self::escape($item->get_description());

        $url = $item->get_permalink();

        if ($url) {
            $output .= '<br>' . self::link('[ Read more ]', $url, [
                'target' => '_blank',
            ]) . '<br>';
        }

        $output .= '<br>';

        return $output;
    }

    /**
     * Convert an array of key value pairs to HTML attributes
     */
    protected static function attrsToString(array $attrs = []): string
    {
        if (!empty($attrs)) {
            $bits = [];

            foreach ($attrs as $key => $value) {
                $bits[] = $key . '="' . $value . '"';
            }

            return ' ' . implode(' ', $bits);
        } else {
            return '';
        }
    }

    public static function escape(string $input): string
    {
        return e($input);
    }

    public static function nl(int $num = 1): string
    {
        return str_repeat('<br>', $num);
    }

    public static function nbsp(int $num = 1): string
    {
        return str_repeat('&nbsp;', $num);
    }

    public static function diaryEntry(DiaryEntry $entry): string
    {
        $output = static::title($entry->date->format('l jS \\of F Y')) . static::nl();
        $output .= e($entry->content);

        if (!empty($entry->meta)) {
            $output .= static::nl(2) . 'Metadata:' . static::nl();

            foreach ($entry->meta as $key => $value) {
                $output .= static::grey(e($key) . ': ' . e($value) . static::nl());
            }
        }

        if ($entry->tags->isNotEmpty()) {
            $output .= static::nl(2) . 'Tags:' . static::nl();
            foreach ($entry->tags as $tag) {
                $output .= static::grey('@' . e($tag->tag) . static::nl());
            }
        }

        return $output;
    }

    public static function diaryEntryList(Collection $entries): string
    {
        $chunks = [];

        $entries->each(function ($entry) use (&$chunks) {
            $chunks[] = static::title($entry->date->toDateString(), [
                'data-type' => 'autofill',
                'data-autofill' => 'diary:edit -d ' . $entry->date->toDateString()
            ]);
            $chunks[] = e(Str::substr($entry->content, 0, 100));
        });

        return static::listToTable(
            $chunks,
            2,
            false,
            ['Date', 'Content']
        );
    }
}
