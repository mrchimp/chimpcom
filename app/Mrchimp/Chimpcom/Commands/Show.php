<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Models\Memory;
use Mrchimp\Chimpcom\Format;
use DB;
// use Mrchimp\Chimpcom\Models\Tag; @todo - add tags

class Show extends LoggedInCommand
{

  public function process() {
    $user = Auth::user();
    $distinct   = $this->input->isFlagSet(['--distinct', '-w']);
    $public     = $this->input->isFlagSet(['--public',   '-p']);
    $private    = $this->input->isFlagSet(['--private',  '-P']);
    $mine       = $this->input->isFlagSet(['--mine',     '-m']);

    if ($this->input->get(1) === 'words') {
      $memories = DB::select('SELECT DISTINCT name FROM memories');
      $words = [];

      foreach ($memories as $word) {
        $words[] = '<span data-type="autofill" data-autofill="show '.e($word->name).'">'.e($word->name).'</span>';
      }

      $this->response->say(Format::listToTable($words, 6, false));
      return;
    }

    if ($public) {
      $item_type = 'public';
    } else if ($private) {
      $item_type = 'private';
    } else if ($mine) {
      $item_type = 'mine';
    } else {
      $item_type = 'both';
    }

    $memories = Memory::visibility($item_type)->orderBy('name')->orderBy('id')->with('user');

    if (is_numeric($this->input->get(1))) {
      $memory_id = $this->input->get(1);
      $memories = $memories->where('id', $memory_id);
    } else if ($this->input->get(1) === 'last') {
      $count = $this->input->get(2);

      $memories = $memories->take(20);
    } else {
      $memories = $memories->where('name', $this->input->get(1))
        ->where('user_id', $user->id);
    }

    $memories = $memories->get();

    if ($memories->count() === 0) {
      $this->response->error('Nothing found.');
      return;
    }

    $this->response->say(Format::memories($memories));
  }

}