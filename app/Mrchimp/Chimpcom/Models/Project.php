<?php

/**
 * Chimpcom Todo list Project model
 */

namespace Mrchimp\Chimpcom\Models;

use Illuminate\Database\Eloquent\Model;
use Mrchimp\Chimpcom\Models\Task;

/**
 * Chimpcom Todo list Project model
 */
class Project extends Model
{

    public function user()
    {
        return $this->belongsTo('app\User');
    }

    public function activeUsers()
    {
        return $this->hasMany('app\User', 'active_project_id');
    }

    public function tasks()
    {
        return $this->hasMany('Mrchimp\Chimpcom\Models\Task');
    }

    public function delete()
    {
        Task::where('project_id', $this->id)->delete();
        return parent::delete();
    }

    public function scopeNameOrId($query, $identifier)
    {
        if (is_numeric($identifier)) {
            $query->where('id', $identifier);
        } else {
            $query->where('name', $identifier);
        }

        return $query;
    }
}
