<?php

namespace App\Http\Controllers;

use Chimpcom;
use Illuminate\Http\Request;

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
            return $response->getJsonResponse();
        } else {
            return $response->getTextOutput();
        }
    }

    public function commandList()
    {
        $commands = Chimpcom::getCommandList();
        return json_encode($commands);
    }

    public function tabComplete(Request $request)
    {
        return response()->json('');

        // @todo fix tabcomplete on all commands
        // $cmd_in = $request->input('cmd_in');
        // $cmd_name = Arr::first(explode(' ', $cmd_in));
        // $input = new StringInput($cmd_in);
        // $command = Chimpcom::instantiateCommand($cmd_name);
        // $result = $command->tabcomplete($input);

        // return json_encode($result);
    }
}
