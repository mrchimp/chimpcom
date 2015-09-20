<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Mrchimp\Chimpcom\Models\Memory;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function memories() {
        return $this->hasMany('Mrchimp\Chimpcom\Models\Memory');
    }

    public function projects() {
        return $this->hasMany('Mrchimp\Chimpcom\Models\Project');
    }

    public function activeProject() {
        return $this->belongsTo('Mrchimp\Chimpcom\Models\Project', 'active_project_id');
    }

    public function tasks() {
        return $this->hasMany('Mrchimp\Chimpcom\Models\Task');
    }
}
