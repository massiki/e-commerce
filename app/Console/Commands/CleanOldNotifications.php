<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('notifications:clean')]
#[Description('Delete notifications older than 7 days that have been read')]
class CleanOldNotifications extends Command
{
    public function handle()
    {
        $count = Notification::where('is_read', true)
            ->whereDate('created_at', '<=', now()->subDays(7))
            ->delete();

        $this->info("Cleaned up {$count} old notification(s).");
    }
}
