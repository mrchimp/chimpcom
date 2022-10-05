<?php

namespace Mrchimp\Chimpcom;

use App\Mrchimp\Chimpcom\Id;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Models\Feed;

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
                $output .= static::listToTable($chunks, 5) . '<br>';
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
                    'data-autofill', e("setpublic $hexid --private")
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
                $chunks[] = static::grey(static::autoLink(e($memory->content)), $attrs);
            } else {
                $chunks[] = static::style(static::autoLink(e($memory->content)), '', $attrs);
            }

            $previous_name = $memory->name;
        }

        $output .= static::listToTable($chunks, 5) . '<br>';

        if (count($memories) > 5) {
            $output .= '<br>' . count($memories) . ' memories found.';
        }

        return $output;
    }

    /**
     * Replace URLs in text with html links.
     */
    public static function autoLink(string $text): string
    {
        $pattern  = '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#';

        $callback = function ($matches) {
            $url       = array_shift($matches);
            $url_parts = parse_url($url);

            $text = preg_replace("/^www./", "", $url_parts["host"]) . (isset($url_parts["path"]) ? "/..." : "");

            return sprintf('<a rel="nofollow" href="%s">%s</a>', $url, $text);
        };

        return preg_replace_callback($pattern, $callback, $text);
    }

    /**
     * Format todo list tasks
     */
    public static function tasks(Collection $tasks, $show_dates = false, $show_project = false): string
    {
        $output = 'ID Priority Description';

        $output .= static::grey(
            ($show_dates ? ' Created' : '') . ' Completed' .
            ($show_project ? ', Project' : '') . '<br><br>'
        );

        foreach ($tasks as $task) {
            $hex_id = Id::encode($task->id);

            $output .= static::style(($task->completed ? '&#10004;' : '') . " $hex_id ", '', [
                'data-type' => 'autofill',
                'data-autofill' => "done $hex_id"
            ]);

            if ($task->priority > 10) {
                $color = '#f00';
            } elseif ($task->priority > 5) {
                $color = '#ff0';
            } elseif ($task->priority < 0) {
                $color = '#666';
            } else {
                $color = '#ccc';
            }

            $priority = ' <span style="color:' . $color . '">' . $task->priority . '</span> ';

            $output .= static::style($priority, '', [
                'data-type' => 'autofill',
                'data-autofill' => 'priority ' . $hex_id
            ]);

            if ($task->completed) {
                $output .= static::grey(' ' . e($task->description));
                $output .= static::grey(' (' . $task->time_completed . ')');
            } else {
                $output .= ' ' . e($task->description);
            }

            if ($show_dates) {
                $output .= static::grey(' (' . $task->created_at . ')');
            }

            if ($show_project) {
                $output .= static::grey(' (' . $task->project->name . ')');
            }

            $output .= '<br>';
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
}
