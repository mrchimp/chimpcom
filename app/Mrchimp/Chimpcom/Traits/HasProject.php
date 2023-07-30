<?php

namespace App\Mrchimp\Chimpcom\Traits;

use Mrchimp\Chimpcom\Models\Project;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasProject
{
    /**
     * That project that this relates to
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
