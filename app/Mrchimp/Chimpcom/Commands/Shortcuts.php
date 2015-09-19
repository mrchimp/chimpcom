<?php

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Models\Shortcut;

class Shortcuts extends AbstractCommand
{

    public function process() {
        $shortcuts = Shortcut::get();

        if (count($shortcuts) === 0) {
            $this->response->error('The are currently no shortcuts.');
            return;
        }

        foreach ($shortcuts as $shortcut) {
            $this->response->say($shortcut->name . ' - ' . $shortcut->url . '<br>');
        }
    }

}