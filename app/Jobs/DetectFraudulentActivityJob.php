<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\AuditLog;
use App\Models\User;
use App\Notifications\FraudAlertNotification;
use Illuminate\Support\Facades\Notification;

class DetectFraudulentActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     * Analisa anomali: Cek apakah user ini melakukan pembatalan (void) / retur lebih dari 3 kali dalam 1 jam terakhir.
     */
    public function handle(): void
    {
        $oneHourAgo = now()->subHour();

        $suspiciousCount = AuditLog::where('user_id', $this->userId)
            ->whereIn('action', ['void', 'refund', 'deleted'])
            ->where('created_at', '>=', $oneHourAgo)
            ->count();

        if ($suspiciousCount >= 3) {
            $user = User::find($this->userId);
            
            Log::alert('POSSIBLE FRAUD DETECTED', [
                'user_id' => $user->id,
                'name'    => $user->name,
                'voids_in_last_hour' => $suspiciousCount
            ]);

            // Notify Admins
            $admins = User::whereHas('roles', function ($q) {
                $q->where('slug', 'admin');
            })->get();

            // In a real app, you would send an email/slack notification here
            // Notification::send($admins, new FraudAlertNotification($user, $suspiciousCount));
        }
    }
}
