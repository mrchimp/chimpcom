<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Validator;
use Mrchimp\Chimpcom\Models\Shortcut;

class Addshortcut extends LoggedInCommand
{

    public function process() {
        $user = Auth::user();

        if (!$user->is_admin) {
            $this->error('No.');
            return;
        }

        if ($this->input->get(2) === false) {
            $this->error('Not enough params.');
            return false;
        }

        $data = [
            'name' => $this->input->get(1),
            'url' => $this->input->get(2)
        ];

        $rules = [
            'name' => 'required',
            'url' => 'required|url'
        ];

        if (!$this->validateOrDie($data, $rules)) {
            return;
        }

        $shortcut = new Shortcut();
        $shortcut->name = $data['name'];
        $shortcut->url = $data['url'];

        if ($shortcut->save()) {
            $this->response->alert('Ok.');
        } else {
            $this->response->error('There was an error. Try again.');
        }
    }

}