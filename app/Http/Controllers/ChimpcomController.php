<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mrchimp\Chimpcom\Chimpcom;

/**
 * Handle Chimpcom HTTP requests
 */
class ChimpcomController extends Controller
{

  /**
   * Provide a response to a given command.
   * If response is an AJAX response, return JSON;
   * 
   * @return String The command output. HTML or JSON.
   */
  public function respond(Request $request) {
    $input = $request->input('cmd_in');
    $chimpcom = new Chimpcom($request);
    $response = $chimpcom->respond($input);

    if ($request->ajax()) {
      return $response->getJson();
    } else {
      return $response->getTextOutput();
    }
  }

}