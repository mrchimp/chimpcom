<?php

namespace Mrchimp\Chimpcom\Actions;

use Auth;
use Hash;
use Session;
use Mrchimp\Chimpcom\Commands\LoggedInCommand;

class Chpass_2 extends LoggedInCommand
{

    public function process()
    {
        $password = $this->input->get(0);

        if (!$password || $password === 'cancel') {
            $this->response->error('Abandoning.');
            $this->response->usePasswordInput(false);
            $this->setAction('normal');
            Session::forget('chpass_1');
            return;
        }

        $data = [
            'password' => Session::get('chpass_1'),
            'password_confirmation' => $password
        ];

        $rules = [
            'password' => 'required|confirmed|min:6'
        ];

        if (!$this->validateOrDie($data, $rules)) {
            return;
        }

        $user = Auth::user();
        $user->password = Hash::make($password);
        $user->save();

        Session::forget('chpass_1');

        $this->response->alert('Ok then. All done.');
        $this->setAction('normal');
        $this->response->usePasswordInput(false);
    }

}
