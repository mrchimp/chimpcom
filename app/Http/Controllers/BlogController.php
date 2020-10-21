<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Mrchimp\Chimpcom\Exceptions\InvalidPathException;
use Mrchimp\Chimpcom\Filesystem\Path;
use Mrchimp\Chimpcom\Models\Directory;
use Parsedown;

class BlogController extends Controller
{
    public function index(string $username): View
    {
        try {
            $path = Path::make('/home/' . $username . '/blog');
            // $dir = Directory::fromPath();
        } catch (InvalidPathException $e) {
            abort(404);
        }

        if (!$path->exists()) {
            abort(404);
        }

        if (!$path->isDirectory()) {
            abort(404);
        }

        return view('blog.index', [
            'title' => 'The Blog of ' . $username,
            'username' => $username,
            'files' => $path->target()->files()->orderBy('updated_at')->get(),
        ]);
    }

    /**
     * SHow a blog post
     *
     * @param string $user
     * @param string $name
     * @return void
     */
    public function show(string $username, string $filename): View
    {
        try {
            $path = Path::make('/home/' . $username . '/blog');
        } catch (InvalidPathException $e) {
            abort(404);
        }

        if (!$path->exists()) {
            abort(404);
        }

        if (!$path->isDirectory()) {
            abort(404);
        }

        $file = $path->target()->files->firstWhere('name', $filename);

        if (!$file) {
            abort(404);
        }

        $parsedown = new Parsedown();
        $parsedown->setSafeMode(true);
        $parsedown->setMarkupEscaped(true);
        $content = $parsedown->text($file->content);

        return view('blog.show', [
            'title' => $file->name,
            'content' => $content,
        ]);
    }
}
