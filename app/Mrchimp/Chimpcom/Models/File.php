<?php

namespace Mrchimp\Chimpcom\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mrchimp\Chimpcom\Filesystem\FilesystemEntity;

class File extends Model implements FilesystemEntity
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'content',
        'owner_id',
        'diurectory_id',
    ];

    /**
     * Directory that this file is in
     */
    public function directory(): BelongsTo
    {
        return $this->belongsTo(Directory::class);
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
            '📄',
            e($this->ownerName()),
            $this->updated_at->format('M j H:i'),
            e($this->name)
        ];
    }
}
