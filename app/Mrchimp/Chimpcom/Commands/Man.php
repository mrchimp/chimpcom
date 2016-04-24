<?php
/**
 * Read the manual
 */

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Chimpcom;
use Mrchimp\Chimpcom\Input;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Man as ManPage;

/**
 * Read the manual
 */
class Man extends AbstractCommand
{

    protected $title = 'Man';
    protected $description = 'Gets help.';
    protected $usage = 'man &lt;command_name&gt;';
    protected $example = 'man projects';
    protected $see_also = '';

    /**
     * Run the command
     */
    public function process() {
        if ($this->input->get(1) === false) {
            $this->response->say('This is how you get help. Type <code>man man</code> for more help on the help.');
            return;
        }

        $page_name = Input::getAlias($this->input->get(1));
        $command = Chimpcom::getCommand($page_name);

        if (!$command) {
          $this->response->error('No man page found');
          return;
        }

        $text = $command->man();
        $this->response->say($text);
        return;
    }

}
