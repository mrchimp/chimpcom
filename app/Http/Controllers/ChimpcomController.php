<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\View\View;
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
     */
    public function index(): View
    {
        return view('index');
    }

    /**
     * Provide a response to a given command.
     *
     * If response is an AJAX response, return JSON
     */
    public function respond(Request $request)
    {
        $input = $request->input('cmd_in');
        $content = $request->input('content');
        $response = Chimpcom::respond($input, $content);

        if ($request->ajax()) {
            return $response->getJsonResponse();
        } else {
            return $response->getTextOutput();
        }
    }

    /**
     * Get a list of commands
     */
    public function commandList(): JsonResponse
    {
        return response()->json(Chimpcom::getCommandList());
    }

    /**
     * Get tab completions
     */
    public function tabComplete(Request $request): JsonResponse
    {
        $chunks = explode(' ', $request->input('cmd_in'));
        $input = new StringInput(implode(' ', array_slice($chunks, 1)));
        $output = new Output;
        $command = Chimpcom::instantiateCommand(Arr::first($chunks));

        return response()->json($command->tabcomplete($input, $output));
    }
}
