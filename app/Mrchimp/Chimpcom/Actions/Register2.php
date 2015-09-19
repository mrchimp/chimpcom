<?php
/**
 * Handle second password input and create account
 */

namespace Mrchimp\Chimpcom\Actions;

use Auth;
use Session;
use Validator;
use App\User;
use Mrchimp\Chimpcom\Commands\AbstractCommand;

/**
 * Handle second password input and create account
 * @action normal
 */
class Register2 extends AbstractCommand
{

    /**
     * Run the command
     */
    public function process() {
        $username = session('register_username');
        $password = session('register_password');
        $password2 = $this->input->get(0);

        if (!$username || !$password) {
            $this->response->error('This should not happen.');
            $this->setAction('normal');
            $this->response->usePasswordInput(false);
            Session::forget('register_username');
            Session::forget('register_password');
            return;
        }

        $user_data = [
            'name' => $username,
            'email' => '',
            'password' => $password,
            'password_confirmation' => $password2
        ];

        $validator = $this->validator($user_data);

        if ($validator->fails()) {

            $errors = $validator->errors();

            foreach ($errors->all() as $error) {
                $this->response->error($error);
            }

            $this->setAction('normal');
            $this->response->usePasswordInput(false);
            return;
        }

        Auth::login($this->create($user_data));

        $this->response->say('Hello, ' . e($user_data['name']) . '! Welcome to Chimpcom.');
        $this->setAction('normal');
        $this->response->usePasswordInput(false);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'email|max:255|unique:users',
            'password' => 'required|confirmed|min:6'
        ]);
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