<?php 
/**
 * Read the manual
 */

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Chimpcom;
use Mrchimp\Chimpcom\Input;
use Mrchimp\Chimpcom\Models\Man as ManPage;

/**
 * Read the manual
 */
class Man extends AbstractCommand
{

    /**
     * Run the command
     */
    public function process() {
        if ($this->input->get(1) === false) {
            $this->response->say('This is how you get help. Type <code>man man</code> for more help on the help.');
            return;
        }

        // View a man page
        $page_name = Input::getAlias($this->input->get(1));
        $man = ManPage::where('command', $page_name)->first();

        if (!$man) {
            $this->response->error('Man page does not exist.');
            return;
        }

        $this->response->title($man->command.'<br>');
        $this->response->say($man->description);

        if ($man->usage) {
            $this->response->title('<br><br>Usage<br>');
            $this->response->say($man->usage);
        }

        if ($man->example) {
            $this->response->title('<br><br>Example<br>');
            $this->response->say($man->example);
        }

        if ($man->see_also) {
            $this->response->title('<br><br>See also<br>');
            $this->response->say($man->see_also);
        }

        if ($man->aliases) {
            $this->response->title('<br><br>Aliases<br>');
            $this->response->say($man->aliases);
        }
    }

}