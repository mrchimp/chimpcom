<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Input;

abstract class AdminCommand extends LoggedInCommand {

    public function run (Input $input) {
        $user = Auth::user();

        if (!$user->is_admin) {
            $this->response->error('No.');
            return $this->response;
        }

        parent::run($input);
        return $this->response;
    }

}