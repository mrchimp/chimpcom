<?php 
/**
 * How some fake server info
 */

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Chimpcom;

/**
 * How some fake server info
 */
class Uname extends AbstractCommand
{

    /**
     * Run the command
     */
    public function process() {
        $flags = [
            's' => ['--kernel-name', '-s'],
            'n' => ['--nodename', '-n'],
            'r' => ['--kernel-version', '-v'],
            'v' => ['--kernel-release', '-r'],
            'm' => ['--machine', '-m'],
            'p' => ['--processor', '-p'],
            'i' => ['--hardware-platform', '-i'],
            'o' => ['--operating-system', '-o']
        ];

        $bits = [
            's' => 'Chimpcom',
            'n' => $_SERVER['HTTP_HOST'],
            'r' => Chimpcom::VERSION,
            'v' => date('d M Y H:i:s'),
            'p' => 'unknown',
            'i' => 'unknown',
            'm' => 'unknown',
            'o' => 'Interwebs'
        ];

        if ($this->input->isFlagSet(['--all', '-a'])) {
            $this->response->say(implode(' ', $bits));
            return true;
        }

        foreach ($flags as $key => $flag) {
            if ($this->input->isFlagSet($flag)) {
              $this->response->say($key . '<br>');
                $this->response->say($bits[$key]);
                return true;
            }
        }

        $this->response->say($bits['s']);
    }

}