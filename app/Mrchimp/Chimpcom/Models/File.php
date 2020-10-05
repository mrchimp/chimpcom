<?php

namespace Mrchimp\Chimpcom\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model
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
}
