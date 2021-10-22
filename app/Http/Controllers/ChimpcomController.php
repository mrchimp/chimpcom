<?php

namespace App\Http\Controllers;

use App\Mrchimp\Chimpcom\Responder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Mrchimp\Chimpcom\Facades\Chimpcom;

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
        if (config('app.env') === 'production' && $request->method() !== 'POST') {
            abort(405);
        }

        $input = $request->input('cmd_in');
        $content = $request->input('content');
        $response = (new Responder($input, $content))->run();

        if ($request->ajax()) {
            return $response->getJsonResponse();
        } else {
            return $response->getTextOutput();
        }
    }

    /**
     * Get a list of commands
     */
    public function commandList(Request $request): JsonResponse
    {
        if (config('app.env') === 'production' && $request->method() !== 'POST') {
            abort(405);
        }

        return response()->json(Chimpcom::getCommandList());
    }

    /**
     * Get tab completions
     */
    public function tabComplete(Request $request): JsonResponse
    {
        if (config('app.env') === 'production' && $request->method() !== 'POST') {
            abort(405);
        }

        return (new Responder($request->input('cmd_in')))->tabComplete();
    }
}
