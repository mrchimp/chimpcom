<?php

namespace App\Listeners;

use DB;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AuthLoginEventHandler
{
    /**
     * Create the event listener.
     *
     * @param Request $request
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;
        $user->update([
            'last_seen' => DB::raw('NOW()'),
        ]);
    }
}
