<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public static function send(string $type, string $title, array $data = []): Notification
    {
        return Notification::create([
            'user_id' => null,
            'type' => $type,
            'title' => $title,
            'data' => $data,
        ]);
    }
}
