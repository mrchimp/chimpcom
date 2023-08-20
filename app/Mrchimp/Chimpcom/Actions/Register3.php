<?php

namespace Mrchimp\Chimpcom\Actions;

use Mrchimp\Chimpcom\Actions\Action;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Facades\Format;
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
        $username  = $input->getActionData('username');
        $password  = $input->getActionData('password');
        $password2 = $input->getActionData('password2');
        $email     = $input->getArgument('email');

        Chimpcom::delAction($input->getActionId());

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
            return 1;
        }

        Auth::login($this->create($data));

        $output->write('Hello, ' . Format::escape($data['name']) . '! Welcome to Chimpcom.');
        $output->populateUserDetails();
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
                'name' => Format::escape($user->name),
                'owner_id' => $user->id,
            ]));
        }

        return $user;
    }
}
