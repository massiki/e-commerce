<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\LogActivityService;

class NotificationController extends Controller
{
    public function markAsRead(Notification $notification)
    {
        $notification->update(['is_read' => true]);

        LogActivityService::log("Marked notification #{$notification->id} as read");

        return response()->json(['message' => 'OK']);
    }

    public function readAll()
    {
        Notification::forAdmin()->unread()->update(['is_read' => true]);

        LogActivityService::log('Marked all notifications as read');

        return redirect()->back();
    }
}
