<?php

namespace App\Console\Commands;

use App\Models\Complaint;
use App\Models\User;
use App\Notifications\ComplaintOverdueReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class RemindOverdueComplaints extends Command
{
    protected $signature = 'complaints:remind-overdue';
    protected $description = 'Kirim reminder ke admin untuk pengaduan yang sudah melewati batas SLA';

    public function handle(): int
    {
        $overdue = Complaint::overdue()
            ->whereNotIn('status', ['resolved', 'rejected'])
            ->with(['category', 'user'])
            ->get();

        if ($overdue->isEmpty()) {
            $this->info('Tidak ada pengaduan yang terlambat.');
            return self::SUCCESS;
        }

        $admins = User::where('role', 'admin')->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new ComplaintOverdueReminder($overdue));
        }

        $this->info("Reminder terkirim untuk {$overdue->count()} pengaduan yang terlambat.");
        return self::SUCCESS;
    }
}