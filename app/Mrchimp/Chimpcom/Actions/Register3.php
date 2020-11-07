<?php

namespace Mrchimp\Chimpcom\Actions;

use App\Mrchimp\Chimpcom\Actions\Action;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Filesystem\Path;
use Mrchimp\Chimpcom\Models\Directory;
use Mrchimp\Chimpcom\Traits\LogCommandNameOnly;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Handle email input and create an account
 */
class Register3 extends Action
{
    use LogCommandNameOnly;

    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('register3');
        $this->setDescription('Register step 4.');
        $this->addArgument(
            'email',
            InputArgument::REQUIRED,
            'Email for new account.'
        );
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username  = Session::get('register_username');
        $password  = Session::get('register_password');
        $password2 = Session::get('register_password2');
        $email     = $input->getArgument('email');

        $data = [
            'name'                  => $username,
            'email'                 => $email,
            'password'              => $password,
            'password_confirmation' => $password2
        ];

        $validator = Validator::make($data, [
            'name'     => 'required|max:255|unique:users',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:8'
        ]);

        if ($validator->fails()) {
            $output->writeErrors($validator, 'Something went wrong. Please try again.');
            Session::forget('register_username');
            Session::forget('register_password');
            Session::forget('register_password2');
            Chimpcom::setAction('normal');
            return 1;
        }

        Auth::login($this->create($data));

        Session::forget('register_username');
        Session::forget('register_password');
        Session::forget('register_password2');

        $output->write('Hello, ' . e($data['name']) . '! Welcome to Chimpcom.');
        $output->populateUserDetails();
        Chimpcom::setAction('normal');
        $output->usePasswordInput(false);

        return 0;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'active_project_id' => -1,
        ]);

        $home_dir = Path::make('/home');

        if ($home_dir->exists()) {
            $home_dir->target()->appendNode(Directory::create([
                'name' => e($user->name),
                'owner_id' => $user->id,
            ]));
        }

        return $user;
    }
}
