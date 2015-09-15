<?php

/**
 * Chimpcom Todo list Task
 */

namespace Mrchimp\Chimpcom\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Chimpcom Todo list Task
 */
class Task extends Model
{

	/**
	 * Get the user that made this task
	 */
	public function user() {
		return $this->belongsTo('App\User');
	}

	/**
	 * Get the project that this task if for
	 */
	public function project() {
		return $this->belongsTo('Project');
	}

}