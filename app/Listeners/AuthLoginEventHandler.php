<?php

namespace App\Listeners;

use Carbon\Carbon;
use Illuminate\Auth\Events\Login;

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
            'last_seen' => Carbon::now(),
        ]);
    }
}
