<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('activity-log:clean')]
#[Description('Delete activity logs older than 7 days')]
class CleanActivityLog extends Command
{
    public function handle()
    {
        $count = ActivityLog::whereDate('created_at', '<=', now()->subDays(7))
            ->delete();

        $this->info("Cleaned up {$count} old activity log(s).");
    }
}
