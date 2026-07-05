<?php

namespace App\Notifications;

use App\Models\Complaint;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ComplaintStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Complaint $complaint,
        public ?string $note = null
    ) {
    }

    // Channel notifikasi: email + WhatsApp (Fonnte)
    public function via($notifiable): array
    {
        return ['mail', FonnteChannel::class];
    }

    public function toMail($notifiable): MailMessage
    {
        $badge = $this->complaint->statusBadge();

        $mail = (new MailMessage)
            ->subject('Update Status Pengaduan - ' . $this->complaint->complaint_number)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Status pengaduan Anda baru saja diperbarui oleh admin.')
            ->line('**Nomor Tiket:** ' . $this->complaint->complaint_number)
            ->line('**Judul:** ' . $this->complaint->title)
            ->line('**Status Terbaru:** ' . $badge['label']);

        if ($this->complaint->status === 'rejected' && $this->complaint->rejection_reason) {
            $mail->line('**Alasan Penolakan:** ' . $this->complaint->rejection_reason);
        }

        if ($this->note) {
            $mail->line('**Catatan Admin:** ' . $this->note);
        }

        return $mail
            ->action('Lihat Detail Pengaduan', route('complaints.show', $this->complaint->id))
            ->line('Terima kasih telah menggunakan layanan pengaduan kami.');
    }

    // Isi pesan untuk WhatsApp
    public function toFonnte($notifiable): string
    {
        $badge = $this->complaint->statusBadge();

        $text = "Halo {$notifiable->name},\n\n";
        $text .= "Status pengaduan Anda telah diperbarui:\n";
        $text .= "No. Tiket: {$this->complaint->complaint_number}\n";
        $text .= "Judul: {$this->complaint->title}\n";
        $text .= "Status: {$badge['label']}\n";

        if ($this->complaint->status === 'rejected' && $this->complaint->rejection_reason) {
            $text .= "Alasan Penolakan: {$this->complaint->rejection_reason}\n";
        }

        if ($this->note) {
            $text .= "Catatan Admin: {$this->note}\n";
        }

        $text .= "\nLihat detail: " . route('complaints.show', $this->complaint->id);
        $text .= "\n\n— SIPMAS, Sistem Pengaduan Masyarakat";

        return $text;
    }
}