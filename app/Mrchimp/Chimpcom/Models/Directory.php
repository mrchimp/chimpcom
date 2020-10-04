<?php

namespace Mrchimp\Chimpcom\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Kalnoy\Nestedset\NodeTrait;

class Directory extends Model
{
    use NodeTrait;

    /**
     * Get the  current directory for a user
     */
    public static function current($user = null): ?Directory
    {
        if (is_null($user)) {
            $user = Auth::user();
        }

        if ($user && $user->currentDirectory) {
            return $user->currentDirectory;
        } elseif (Session::has('current_directory')) {
            return Session::get('current_directory');
        } else {
            $default = Directory::default();

            if ($default) {
                $default->setCurrent();
            }

            return $default;
        }

        return Directory::default();
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
     *
     * @todo Use a better default
     */
    public static function default(): ?Directory
    {
        return Directory::query()->whereIsRoot()->first();
    }

    /**
     * Get the name of the owner of this directory
     *
     * @return string
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
        if ($this->ancestors->isEmpty()) {
            return '/';
        }

        $this->ancestors->shift();

        return '/' .
            $this
                ->ancestors
                ->sortBy($this->getLftName())
                ->pluck('name')
                ->push($this->name)
                ->join('/');
    }
}
