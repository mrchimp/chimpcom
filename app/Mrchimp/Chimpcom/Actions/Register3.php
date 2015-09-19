<?php

namespace Mrchimp\Chimpcom\Actions;

use Auth;
use Session;
use Validator;
use App\User;
use Mrchimp\Chimpcom\Commands\AbstractCommand;

class Register3 extends AbstractCommand
{

    public function process() {
        $username = Session::get('register_username');
        $password = Session::get('register_password');
        $password2 = Session::get('register_password2');
        $email = $this->input->get(0);

        $data = [
            'name' => $username,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password2
        ];

        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6'
        ];

        if (!$this->validateOrDie($data, $rules)) {
            return;
        }

        Auth::login($this->create($data));

        Session::forget('register_username');
        Session::forget('register_password');
        Session::forget('register_password2');

        $this->response->say('Hello, ' . e($data['name']) . '! Welcome to Chimpcom.');
        $this->setAction('normal');
        $this->response->usePasswordInput(false);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

}