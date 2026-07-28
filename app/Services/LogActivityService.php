<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class LogActivityService
{
    public static function log(string $activity): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => $activity,
        ]);
    }
}
