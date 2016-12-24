<?php
/**
 * Give details on the current user
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Give details on the current user
 */
class Who extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('who');
        $this->setDescription('Give details on the current user.');
        $this->addArgument(
            'inputs',
            InputArgument::IS_ARRAY,
            'Varies depending on use.'
        );
    }

    /**
     * Run the command
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputs = $input->getArgument('inputs');
        $part_1 = isset($inputs[0]) ? $inputs[0] : null;
        $part_2 = isset($inputs[1]) ? $inputs[1] : null;

        if ($part_1 === 'are' &&
                 ( $part_2 === 'you?' ||
                   $part_2 === 'you' )) {
            $output->write('I am a sentient command line. Be afraid.');
            return true;
        }

        if ($part_1 != null &&
            $part_1 != 'am') {
            $output->write('No idea.');
            return false;
        }

        if ($part_1 === null ||
            ($part_1 === 'am' &&
            ( $part_2 === 'i' ||
              $part_2 === 'i?' )) ){

            $tbl = '<table>';

            if (Auth::check()){
              $user = Auth::user();
              $tbl .= '<tr><td width="150">' .
                        Format::title('USERNAME') .
                        '</td>
                       <td>'.e($user->name).'</td></tr>
                       <tr><td>' .
                        Format::title('USER ID') .
                       '</td>
                       <td>'.$user->id.'</td></tr>
                     <tr>
                     </tr>';
            }

            $tbl .= '<tr><td width="150">' .
                        Format::title('IP ADDRESS') .
                      '</td>
                     <td>'.$_SERVER['REMOTE_ADDR'].'</td></tr>
                     <tr><td>' .
                        Format::title('USERAGENT') .
                      '</td>
                     <td>'.$_SERVER['HTTP_USER_AGENT'].'</td></tr>
                     </table>';

            $output->write($tbl);
            return;
        }

        $output->error('Whut?');
    }
}
