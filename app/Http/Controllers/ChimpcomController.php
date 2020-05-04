<?php

namespace App\Http\Controllers;

use Chimpcom;
// use Mrchimp\Chimpcom\Chimpcom;
use Illuminate\Http\Request;
use Input;
use Mrchimp\Chimpcom\Input as ChimpcomInput;

/**
 * Handle Chimpcom HTTP requests
 */
class ChimpcomController extends Controller
{
    /**
     * Show the interface
     *
     * @return View
     */
    public function index()
    {
        return view('index');
    }

    /**
     * Provide a response to a given command.
     * If response is an AJAX response, return JSON;
     *
     * @return String The command output. HTML or JSON.
     */
    public function respond(Request $request)
    {
        $input = $request->input('cmd_in');
        $response = Chimpcom::respond($input);

        if ($request->ajax()) {
            return $response->getJson();
        } else {
            return $response->getTextOutput();
        }
    }

    public function commandList()
    {
        $commands = Chimpcom::getCommandList();
        return json_encode($commands);
    }

    public function tabComplete()
    {
        $cmd_in = Input::get('cmd_in');
        $input = new ChimpcomInput($cmd_in);
        $command = Chimpcom::getCommand($input->getCommand());
        $result = $command->runTabcomplete($input);
        return json_encode($result);
    }
}
