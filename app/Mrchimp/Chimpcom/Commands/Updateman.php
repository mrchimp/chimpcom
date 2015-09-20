<?php 
/**
 * Update the manual
 *
 * Usage:
 *     updateman create COMMAND
 *     updateman update COMMAND description NEW_DATA
 *     updateman update COMMAND example     NEW_DATA
 *     updateman update COMMAND seealso     NEW_DATA
 *     updateman update COMMAND usage       NEW_DATA
 *     updateman update COMMAND aliases     NEW_DATA
 */

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Chimpcom;
use Mrchimp\Chimpcom\Models\Man as ManPage;

/**
 * Update the manual
 */
class Updateman extends AdminCommand
{

    /**
     * Run the command
     */
    public function process() {
        $action  = $this->input->get(1); // The action to perform. update/create
        $command = $this->input->get(2); // The command whose page we're updating 
        $part    = $this->input->get(3); // The part of the command to update

        if ($action === false) {
            $this->response->error('No action given.');
            return;
        }

        if ($command === false) {
            $this->response->error('No command name given.');
            return;
        }

        switch ($action) {
            case 'update':
                $data = implode(' ', array_slice($this->input->getParamArray(), 3));
                $man = ManPage::where('command', $command)->first();

                if (!$man) {
                    $this->response->error('Man page not found.');
                    return;
                }

                if ($part === false) {
                    $this->response->error('No part given.');
                    return;
                }

                if (!$data) {
                    $this->response->error('No data given.');
                    return;
                }

                if ($part === 'description' || $part === 'desc') {
                    $man->description = $data;
                } else if ($part === 'example') { // Set example
                    $man->example = $data;
                } else if ($part === 'seealso') { // Set see also
                    $man->see_also = $data;
                } else if ($part === 'usage') { // Set usage
                    $man->usage = $data;
                } else if ($part === 'aliases') { // Set usage
                    $man->aliases = $data;
                } else {
                    $this->response->error('I don\'t know.');
                    return;
                }

                $man->save();

                $this->response->alert('Ok.');

                break;
            case 'create':
                $man = new ManPage();
                $man->command = $command;
                $man->save();

                $this->response->alert('Ok.');

                break;
            default:
                $this->response->error('Invalid action.');
        }
    }

}