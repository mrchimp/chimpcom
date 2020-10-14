<?php

namespace Mrchimp\Chimpcom\Filesystem;

use App\User;
use Illuminate\Support\Arr;
use Mrchimp\Chimpcom\Exceptions\InvalidPathException;
use Mrchimp\Chimpcom\Exceptions\PermissionDenied;
use Mrchimp\Chimpcom\Models\Directory;
use Mrchimp\Chimpcom\Models\File;
use PDO;

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
     * Type of target
     *
     * @var int
     */
    protected $type;

    /**
     * The directory/file that this path points at
     *
     * @var Directory|File
     */
    protected $target;

    /**
     * Whether resolve has been called
     *
     * @var boolean
     */
    protected $resolved = false;

    /**
     * The parent directory
     *
     * @var Directory
     */
    protected $parent_directory;

    public const FILE = 0;
    public const DIRECTORY = 1;

    /**
     * Make a new Path
     */
    public function __construct($path)
    {
        $this->path = $path;
        $this->chunks = array_values(array_filter(explode('/', $path)));
        $this->resolve();
        $this->reset();
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
     * Move the cursor back and return the current chunk
     */
    public function previous(): ?string
    {
        $this->index--;

        return $this->get();
    }

    /**
     * Get the current chunk - or null
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
     * Move cursor to last item
     */
    public function toEnd(): void
    {
        $this->index = count($this->chunks) - 1;
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

    /**
     * Find the Directory or File that this path points at
     *
     * @param Directory $source Directory to use as a source for relative paths
     *
     * @throws InvalidPathException
     */
    public function resolve(Directory $source = null): void
    {
        if ($this->resolved) {
            return;
        }

        if ($this->count() > 32) {
            throw new InvalidPathException('Path length is too long.');
        }

        if (!$source) {
            $source = Directory::current();

            if (!$source) {
                throw new InvalidPathException('No source directory.');
            }
        }

        if ($this->path === '.') {
            $this->type = static::DIRECTORY;
            $this->target = $source;
            return;
        }

        if ($this->isRoot()) {
            $this->type = static::DIRECTORY;
            $this->target = Directory::root();
            return;
        }

        if ($this->isEmpty()) {
            throw new InvalidPathException('Path is empty.');
        }

        if ($this->isAbsolute()) {
            $source = Directory::root();
        }

        $current = $source;

        do {
            if ($this->get() === '.') {
                continue;
            }

            if ($this->get() === '..') {
                if ($current->parent) {
                    $next = $current->parent;
                } else {
                    throw new InvalidPathException('No such file or directory');
                }
            } else {
                $next = $current->children->firstWhere('name', $this->get());
            }

            if ($this->isLast()) {
                $this->parent_directory = $current;

                if ($next) {
                    $this->target = $next;
                    $this->type = static::DIRECTORY;
                    return;
                } else {
                    $file = $current->files->firstWhere('name', $this->get());

                    if (!$file) {
                        return;
                    }

                    $this->target = $file;
                    $this->type = static::FILE;
                    return;
                }
            }

            $current = $next;
        } while (!is_null($this->next()));

        throw new \Exception('This should never happen.');
    }

    /**
     * If path resolves to an existing item
     */
    public function exists(): bool
    {
        return !is_null($this->target);
    }

    /**
     * If resolved target is a directory
     */
    public function isDirectory(): bool
    {
        return $this->type === static::DIRECTORY;
    }

    /**
     * If resolved target is a file
     */
    public function isFile(): bool
    {
        return $this->type === static::FILE;
    }

    /**
     * Get the entity that this path points at
     */
    public function target(): FilesystemEntity
    {
        return $this->target;
    }

    /**
     * The parent directory
     */
    public function parent(): ?Directory
    {
        return $this->parent_directory;
    }

    /**
     * Create a directory in the parent directory
     */
    public function makeDirectory(User $owner, string $name): Directory
    {
        if (!$this->parent_directory) {
            throw new InvalidPathException('Parent directory does not exist.');
        }

        if (!$this->parent_directory->belongsToUser($owner)) {
            throw new PermissionDenied('User does not own the parent directory.');
        }

        $directory = Directory::make([
            'name' => $name,
            'owner_id' => $owner->id,
        ]);

        $this->parent_directory->appendNode($directory);

        return $directory;
    }

    /**
     * Create a file in the parent directory
     */
    public function makeFile(User $owner, string $name): File
    {
        if (!$this->parent_directory) {
            throw new InvalidPathException('Parent directory does not exist');
        }

        if (!$this->parent_directory->belongsToUser($owner)) {
            throw new PermissionDenied('User does not own the parent directory.');
        }

        $file = File::make([
            'name' => $name,
            'owner_id' => $owner->id,
            'content' => '',
        ]);

        $this->parent_directory->files()->save($file);

        return $file;
    }

    /**
     * Convert to string
     */
    public function __toString()
    {
        return $this->path;
    }
}
