<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Session;
use Laravel\Sanctum\HasApiTokens;
use Mrchimp\Chimpcom\Models\Project;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_seen',
        'active_project_id'
    ];

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

    public function currentDirectory()
    {
        return $this->belongsTo('Mrchimp\Chimpcom\Models\Directory', 'current_directory_id');
    }

    public function setActiveProject(Project $project)
    {
        Session::put('current_project_id', $project->id);

        $project->activeUsers()->save($this);
    }

    public function messages()
    {
        return $this->hasMany('Mrchimp\Chimpcom\Models\Message', 'recipient_id');
    }
}
