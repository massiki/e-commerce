<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function markAsRead(Notification $notification)
    {
        $notification->update(['is_read' => true]);

        return response()->json(['message' => 'OK']);
    }

    public function readAll()
    {
        Notification::forAdmin()->unread()->update(['is_read' => true]);

        return redirect()->back();
    }
}
