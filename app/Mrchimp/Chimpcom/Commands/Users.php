<?php
/**
 * Get a list of users
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use DB;
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

        $users = DB::table('users')
            ->leftJoin('memories', 'users.id', '=', 'memories.user_id')
            ->leftJoin('projects', 'users.id', '=', 'projects.user_id')
            ->leftJoin('tasks', 'users.id', '=', 'tasks.user_id')
            ->select(DB::raw('users.id, users.name, users.last_seen, count(memories.id) as memory_count, count(projects.id) as project_count, count(tasks.id) as task_count'))
            ->groupBy('users.id')
            ->get();

        $output->say('<table><tr><td>');
        $output->title('id');
        $output->say('</td><td>');
        $output->title('Username');
        $output->say('</td><td>');
        $output->title('Last Seen');
        $output->say('</td><td>');
        $output->title('Memories');
        $output->say('</td><td>');
        $output->title('Projects');
        $output->say('</td><td>');
        $output->title('Tasks');
        $output->say('</td></tr>');

        foreach($users as $user){
            $output->say('<tr><td>'.$user->id.'</td>
                        <td>'.$user->name.'</td>
                        <td>'.$user->last_seen.'</td>
                        <td>'.$user->memory_count.'</td>
                        <td>'.$user->project_count.'</td>
                        <td>'.$user->task_count.'</td></tr>');
        }

        $output->say('</table>');
    }
}
