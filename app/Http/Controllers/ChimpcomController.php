<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Mrchimp\Chimpcom\Console\Output;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Symfony\Component\Console\Input\StringInput;

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
        $chunks = explode(' ', $request->input('cmd_in'));
        $input = new StringInput(implode(' ', array_slice($chunks, 1)));
        $output = new Output;
        $command = Chimpcom::instantiateCommand(Arr::first($chunks));
        $result = $command->tabcomplete($input, $output);

        return json_encode($result);
    }
}
