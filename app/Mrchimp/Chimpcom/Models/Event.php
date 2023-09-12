<?php

namespace Mrchimp\Chimpcom\Models;

use App\Mrchimp\Chimpcom\Traits\HasProject;
use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mrchimp\Chimpcom\Traits\HasTags;

class Event extends Model
{
    use HasFactory, HasProject, HasTags;

    protected $fillable =  [
        'description',
        'user_id',
        'date',
        'project_id',
    ];

    protected $dates = [
        'date',
    ];

    protected static function newFactory()
    {
        return EventFactory::new();
    }

    public function scopeFuture(Builder $query)
    {
        $query->whereDate('date', '>=', now());
    }

    public function scopePase(Builder $query)
    {
        $query->whereDate('date', '<', now());
    }
}
