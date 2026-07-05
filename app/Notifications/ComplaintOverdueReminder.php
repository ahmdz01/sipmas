<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class ComplaintOverdueReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Collection $complaints)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Peringatan: ' . $this->complaints->count() . ' Pengaduan Melewati Batas SLA')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Berikut pengaduan yang sudah melewati batas waktu penanganan (SLA) dan perlu segera ditindak:');

        foreach ($this->complaints->take(10) as $c) {
            $mail->line("• {$c->complaint_number} — {$c->title} (terlambat {$c->overdueHours()} jam, status: {$c->statusBadge()['label']})");
        }

        if ($this->complaints->count() > 10) {
            $mail->line('... dan ' . ($this->complaints->count() - 10) . ' pengaduan lainnya.');
        }

        return $mail->action('Lihat Semua Pengaduan', route('admin.complaints.index', ['overdue' => 1]))
            ->line('Segera tindak lanjuti agar pelayanan tetap tepat waktu.');
    }
}