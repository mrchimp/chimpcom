<?php

namespace Mrchimp\Chimpcom\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
  public function memories() {
    return $this->morphedByMany('Mrchimp\Chimpcom\Models\Memory', 'taggable');
  }
}