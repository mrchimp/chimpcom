<?php

namespace Mrchimp\Chimpcom\Models;

use App\Mrchimp\Chimpcom\Traits\HasProject;
use Database\Factories\DiaryEntryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mrchimp\Chimpcom\Traits\HasTags;

class DiaryEntry extends Model
{
    use HasFactory, HasProject, HasTags;

    protected $table = 'diary_entries';

    protected $fillable = [
        'user_id',
        'date',
        'content',
        'project_id',
    ];

    protected $dates = [
        'date',
    ];

    /**
     * The owner/creator of this memory
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory()
    {
        return DiaryEntryFactory::new();
    }
}
