<?php

namespace Mrchimp\Chimpcom\Filesystem;

use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Mrchimp\Chimpcom\Models\Directory;

class RootDirectory implements FilesystemEntity
{
    /**
     * Child directories
     *
     * @var Collection
     */
    public $children;

    /**
     * Ancestor directories
     *
     * @var Collection
     */
    public $ancestors;

    /**
     * Files in this directory
     *
     * @var Collection
     */
    public $files;

    /**
     * Name of this directory
     *
     * @var string
     */
    public $name = '/';

    /**
     * Lister
     */
    public $lister;

    public function __construct()
    {
        $this->children = Directory::whereIsRoot()->get();
        $this->ancestors = new Collection();
        $this->files = new Collection;
    }

    public function setCurrent($user = null): self
    {
        if (Auth::check() || !is_null($user)) {
            if (is_null($user)) {
                $user = Auth::user();
            }

            $user->currentDirectory()->dissociate();
            $user->save();
        }

        Session::put('current_directory', $this);

        return $this;
    }

    public function load($rel)
    {
        return;
    }

    /**
     * This file belongs to the given user
     */
    public function belongsToUser(User $user): bool
    {
        return false;
    }

    /**
     * The full path of this directory
     */
    public function fullPath(): string
    {
        return '/';
    }
}
