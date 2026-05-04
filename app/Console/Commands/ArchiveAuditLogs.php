<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ArchiveAuditLogs extends Command
{
    protected $signature = 'audit:archive';
    protected $description = 'Archive audit logs older than 6 months to ensure database scalability';

    public function handle()
    {
        $this->info('Starting audit log archiving...');

        $cutoffDate = now()->subMonths(6);

        // 1. Move to archive table
        $count = DB::table('audit_logs')
            ->where('created_at', '<', $cutoffDate)
            ->count();

        if ($count === 0) {
            $this->info('No logs to archive.');
            return;
        }

        DB::transaction(function () use ($cutoffDate) {
            // Copy data to archive
            DB::insert("INSERT INTO audit_logs_archive (user_id, action, model_type, model_id, old_values, new_values, ip_address, user_agent, created_at, updated_at)
                        SELECT user_id, action, model_type, model_id, old_values, new_values, ip_address, user_agent, created_at, updated_at 
                        FROM audit_logs WHERE created_at < ?", [$cutoffDate]);

            // Delete from main table
            DB::table('audit_logs')
                ->where('created_at', '<', $cutoffDate)
                ->delete();
        });

        $this->info("Successfully archived {$count} audit logs.");
    }
}
