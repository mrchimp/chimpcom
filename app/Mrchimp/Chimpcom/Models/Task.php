<?php

/**
 * Todo list task
 */

namespace Mrchimp\Chimpcom\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Todo list task
 */
class Task extends Model
{

    /**
     * The user that made this task
     */
    public function user() {
        return $this->belongsTo('App\User');
    }

    /**
     * The project that this task is for
     */
    public function project() {
        return $this->belongsTo('Mrchimp\Chimpcom\Models\Project');
    }

    public function scopeSearch($query, $search_term = null) {
        if (is_null($search_term)) {
            return $query;
        }

        return $query->where('description', 'LIKE', '%'.$search_term.'%');
    }

    public function scopeCompleted($query, $value = null) {
        if (is_null($value)) {
            return $query;
        }

        return $query->where('completed', $value);
    }

    public function scopeProject($query, $value = null) {
        if (is_null($value)) {
            return $query;
        }

        return $query->where('project_id', $value);
    }

}
