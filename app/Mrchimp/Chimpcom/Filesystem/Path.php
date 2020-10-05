<?php

namespace Mrchimp\Chimpcom\Filesystem;

use Illuminate\Support\Arr;

/**
 * Helper class for handling path strings
 */
class Path
{
    /**
     * Full path as a string
     *
     * @var string
     */
    protected $path;

    /**
     * Array of parts of this string
     *
     * @var array
     */
    protected $chunks;

    /**
     * Index of cursor
     *
     * @var integer
     */
    protected $index = 0;

    /**
     * Make a new Path
     */
    public function __construct($path)
    {
        $this->path = $path;
        $this->chunks = array_values(array_filter(explode('/', $path)));
    }

    /**
     * Make a new path
     */
    public static function make(string $path): Path
    {
        return new static($path);
    }

    /**
     * Get the first chunk
     */
    public function first(): ?string
    {
        return Arr::first($this->chunks);
    }

    /**
     * Get the last chunk
     */
    public function last(): ?string
    {
        return Arr::last($this->chunks);
    }

    /**
     * Move the cursor on and then return the current chunk
     */
    public function next(): ?string
    {
        $this->index++;

        return $this->get();
    }

    /**
     * Get the current chunk
     */
    public function get(): ?string
    {
        return Arr::get($this->chunks, $this->index, null);
    }

    /**
     * Reset index to start
     */
    public function reset(): void
    {
        $this->index = 0;
    }

    /**
     * Is this path to root?
     */
    public function isRoot(): bool
    {
        return $this->path === '/';
    }

    /**
     * Is this an absolute path?
     */
    public function isAbsolute(): bool
    {
        return substr($this->path, 0, 1) === '/';
    }

    /**
     * How many chunks are '..'
     *
     * This is to keep things efficient. Allowing a path that goes
     * up and down repeatedly could cause a bunch of needless db
     * queries and/or processing
     */
    public function doubleDotCount(): int
    {
        return count(array_filter($this->chunks, function ($item) {
            return $item === '..';
        }));
    }

    /**
     * If there are no chunks
     */
    public function isEmpty(): bool
    {
        return count($this->chunks) === 0;
    }

    /**
     * Number of chunks
     */
    public function count(): int
    {
        return count($this->chunks);
    }

    /**
     * Is the index the last item
     */
    public function isLast(): bool
    {
        return $this->index === $this->count() - 1;
    }
}
