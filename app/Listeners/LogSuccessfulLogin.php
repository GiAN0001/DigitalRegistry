<?php

namespace App\Listeners;

use App\Models\Log;
use App\Enums\LogAction;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        // Model casting handles the conversion to string automatically
        Log::create([
            'user_id' => $event->user->id,
            'log_type' => LogAction::AUTH_LOGIN, 
            'description' => "User {$event->user->username} logged into the system.",
            'date' => now(),
        ]);
    }
}