<?php

namespace Mrchimp\Chimpcom\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Kalnoy\Nestedset\NodeTrait;
use Mrchimp\Chimpcom\Exceptions\InvalidPathException;
use Mrchimp\Chimpcom\Filesystem\FilesystemEntity;
use Mrchimp\Chimpcom\Filesystem\Path;
use Mrchimp\Chimpcom\Filesystem\RootDirectory;

class Directory extends Model implements FilesystemEntity
{
    use NodeTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'owner_id',
    ];

    /**
     * Files in this directory
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'directory_id');
    }

    /**
     * Get the  current directory for a user
     */
    public static function current($user = null): ?FilesystemEntity
    {
        if (is_null($user)) {
            $user = Auth::user();
        }

        if ($user && $user->currentDirectory) {
            return $user->currentDirectory;
        } elseif (Session::has('current_directory')) {
            return Session::get('current_directory');
        }

        return new RootDirectory;
    }

    /**
     * Set this directory as the current for the current user
     */
    public function setCurrent($user = null): self
    {
        if (Auth::check() || !is_null($user)) {
            if (is_null($user)) {
                $user = Auth::user();
            }

            $user->currentDirectory()->associate($this)->save();
        } else {
            Session::put('current_directory', $this);
        }

        return $this;
    }

    /**
     * Get the default directory
     */
    public static function default(): ?FileSystemEntity
    {
        if (!Auth::check()) {
            return new RootDirectory;
        }

        $path_str = '/home/' . Auth::user()->name;
        $path = Path::make($path_str);

        if ($path->exists() && $path->isDirectory()) {
            return $path->target();
        }

        return new RootDirectory;
    }

    /**
     * Get the user's home directory
     *
     * @return Directory|null
     */
    public static function home(): ?Directory
    {
        if (!Auth::check()) {
            return null;
        }

        $root = new RootDirectory;

        if (!$root) {
            return null;
        }

        $home = $root->children->firstWhere('name', 'home');

        if (!$home) {
            return null;
        }

        return $home->children->firstWhere('name', Auth::user()->name);
    }

    /**
     * Get the name of the owner of this directory
     */
    public function ownerName(): string
    {
        if ($this->owner) {
            return $this->owner->name;
        }

        return 'root';
    }

    /**
     * Ownder of this directory
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Find a child directory by name
     */
    public function findChild(string $name): ?Directory
    {
        foreach ($this->children as $child) {
            if ($child->name === $name) {
                return $child;
            }
        }

        return null;
    }

    /**
     * The full path of this directory
     */
    public function fullPath(): string
    {
        return '/' .
            $this
                ->ancestors
                ->sortBy($this->getLftName())
                ->pluck('name')
                ->push($this->name)
                ->join('/');
    }

    /**
     * This file belongs to the given user
     */
    public function belongsToUser(User $user): bool
    {
        return (int) $this->owner_id === (int) $user->id;
    }

    /**
     * Get an array of items to show in an LS output
     */
    public function lsArray(): array
    {
        return [
            '📁',
            e($this->ownerName()),
            $this->updated_at->format('M j H:i'),
            e($this->name)
        ];
    }
}
