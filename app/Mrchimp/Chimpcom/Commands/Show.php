<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Chimpcom;
use Mrchimp\Chimpcom\Models\Memory;
use Mrchimp\Chimpcom\Format;
use DB;
use Log;
// use Mrchimp\Chimpcom\Models\Tag; @todo - add tags

class Show extends LoggedInCommand
{

  protected $title = 'Show';
  protected $description = 'Find a memory by its name.';
  protected $usage = 'show &lt;memory_name&gt; [--distinct|-w] [--public|-p] [--mine|-m]';
  protected $example = 'show chimpcom';
  protected $see_also = 'save, find, forget, setpublic';

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

    Log::debug('Getting memory type ' . $item_type);

    $memories = Memory::visibility($item_type);

    if (is_numeric($this->input->get(1))) {
      $memory_id = $this->input->get(1);
      $memories = $memories->where('id', Chimpcom::decodeId($memory_id));
  } else if ($this->input->get(1) === false) {
      $count = $this->input->get(2);

      $memories = $memories->take(20);
    } else {
      $memories = $memories->where('name', $this->input->get(1))
        ->where('user_id', $user->id);
    }

    $memories = $memories
        ->orderBy('name')
        ->orderBy('id')
        ->with('user')
        ->get();

    if ($memories->count() === 0) {
      $this->response->error('Nothing found.');
      return;
    }

    $this->response->say(Format::memories($memories));
  }

}
