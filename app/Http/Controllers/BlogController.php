<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Mrchimp\Chimpcom\Exceptions\InvalidPathException;
use Mrchimp\Chimpcom\Filesystem\Path;
use Parsedown;

class BlogController extends Controller
{
    /**
     * Get a list of a user's blog posts
     */
    public function index(string $username): View
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

        return view('blog.index', [
            'title' => 'The Blog of ' . $username,
            'username' => $username,
            'files' => $path->target()->files()->orderBy('updated_at')->get(),
        ]);
    }

    /**
     * SHow a blog post
     */
    public function show(string $username, string $filename): View
    {
        try {
            $path = Path::make('/home/' . $username . '/blog/' . $filename);
        } catch (InvalidPathException $e) {
            abort(404);
        }

        if (!$path->exists()) {
            abort(404);
        }

        if ($path->isDirectory()) {
            abort(404);
        }

        $parsedown = new Parsedown();
        $parsedown->setSafeMode(true);
        $parsedown->setMarkupEscaped(true);
        $content = $parsedown->text($path->target()->content);

        return view('blog.show', [
            'title' => $path->target()->name,
            'content' => $content,
        ]);
    }
}
