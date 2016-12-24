<?php
/**
 * Get a list of users
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use App\User;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Get a list of users
 */
class Users extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('users');
        $this->setDescription('Get a list of users.');
    }

    /**
     * Run the command
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error('You must log in to use this command.');
            return;
        }

        if (!Auth::user()->is_admin) {
            $output->error('No.');
            return;
        }

        $users = User::get();

        $output->say('<table><tr><td>');
        $output->title('id');
        $output->say('</td><td>');
        $output->title('Username');
        $output->say('</td></tr>');

        foreach($users as $user){
            $output->say('<tr><td>'.$user->id.'</td>
                        <td>'.$user->name.'</td></tr>');
        }

        $output->say('</table>');
    }
}
