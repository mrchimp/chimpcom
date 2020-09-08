<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Mrchimp\Chimpcom\Models\Feed;
use Mrchimp\Chimpcom\Models\Memory;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'last_seen'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function memories()
    {
        return $this->hasMany('Mrchimp\Chimpcom\Models\Memory');
    }

    public function projects()
    {
        return $this->hasMany('Mrchimp\Chimpcom\Models\Project');
    }

    public function activeProject()
    {
        return $this->belongsTo('Mrchimp\Chimpcom\Models\Project', 'active_project_id');
    }

    public function tasks()
    {
        return $this->hasMany('Mrchimp\Chimpcom\Models\Task');
    }

    public function feeds()
    {
        return $this->hasMany('Mrchimp\Chimpcom\Models\Feed');
    }
}
