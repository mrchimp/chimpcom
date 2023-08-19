<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Support\Str;
use App\Http\Requests\DiaryRequest;

class DiaryController extends Controller
{
    public function index(DiaryRequest $request)
    {
        $meta = array_map(fn ($str) => Str::slug($str), $request->input('meta', []));

        $entries = Auth::user()->diaryEntries()->get()->map(function ($entry) use ($meta) {
            $datum = [
                'values' => [],
                'timestamp' => null,
            ];

            foreach ($meta as $key) {
                if (isset($entry->meta[$key]) && is_numeric($entry->meta[$key])) {
                    $datum['values'][$key] = $entry->meta[$key];
                } else {
                    $datum['values'][$key] = null;
                }
            }

            $datum['timestamp'] = $entry->date->toIso8601String();

            return $datum;
        });

        return response()->json([
            'series' => $meta,
            'data' => $entries,
        ]);
    }
}
