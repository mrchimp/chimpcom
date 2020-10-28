<?php

namespace App\Mrchimp\Chimpcom;

use Mrchimp\Chimpcom\Format;

/**
 * Render a progress bar string from a value and total
 */
class ProgressBar
{
    /**
     * String to use for full
     *
     * @var string
     */
    protected $full_pip = '▰';

    /**
     * String to use for empty
     *
     * @var string
     */
    protected $empty_pip = '▱';

    /**
     * Amount completed
     *
     * @var int
     */
    protected $done;

    /**
     * Total amount
     *
     * @var int
     */
    protected $total;

    public function __construct($done, $total)
    {
        $this->done = $done;
        $this->total = $total;
    }

    /**
     * Create a new ProgressBar
     */
    public static function make(int $done, int $total): ProgressBar
    {
        return new ProgressBar($done, $total);
    }

    /**
     * Convert the ProgressBar to a string
     */
    public function toString(int $width = 10, string $full_pip = null, string $empty_pip = null): string
    {
        if ($this->total === 0) {
            return Format::grey(str_repeat($this->empty_pip, $width));
        }

        if ($full_pip) {
            $this->full_pip = $full_pip;
        }

        if ($empty_pip) {
            $this->empty_pip = $empty_pip;
        }

        $done_chunks = ($this->done / $this->total) * $width;
        $done_pips = '';
        $not_done_pips = '';

        for ($i = 0; $i < $done_chunks; $i++) {
            $done_pips .= $this->full_pip;
        }

        for ($i = 0; $i < $width - $done_chunks; $i++) {
            $not_done_pips .= $this->empty_pip;
        }

        return $done_pips . Format::grey($not_done_pips);
    }
}
