<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\View\View;

/**
 * Display diary metadata graphs
 */
class DiaryGraphController extends Controller
{
    /**
     * Show a graph
     */
    public function show(): View
    {
        if (Auth::guest()) {
            abort(403);
        }

        return view('graphs.diary');
    }
}
