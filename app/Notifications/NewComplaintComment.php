<?php

namespace App\Notifications;

use App\Models\Complaint;
use App\Models\ComplaintComment;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewComplaintComment extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Complaint $complaint,
        public ComplaintComment $comment
    ) {
    }

    public function via($notifiable): array
    {
        return [FonnteChannel::class, 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = $notifiable->isAdmin()
            ? route('admin.complaints.show', $this->complaint->id)
            : route('complaints.show', $this->complaint->id);

        return (new MailMessage)
            ->subject('Komentar Baru - ' . $this->complaint->complaint_number)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Ada komentar baru pada pengaduan berikut:')
            ->line('**Nomor Tiket:** ' . $this->complaint->complaint_number)
            ->line('**Judul:** ' . $this->complaint->title)
            ->line('**Dari:** ' . $this->comment->user->name)
            ->line('**Komentar:** ' . $this->comment->message)
            ->action('Lihat & Balas', $url);
    }

    public function toFonnte($notifiable): string
    {
        $url = $notifiable->isAdmin()
            ? route('admin.complaints.show', $this->complaint->id)
            : route('complaints.show', $this->complaint->id);

        $text = "Halo {$notifiable->name},\n\n";
        $text .= "💬 Ada komentar baru pada pengaduan:\n";
        $text .= "No. Tiket: {$this->complaint->complaint_number}\n";
        $text .= "Dari: {$this->comment->user->name}\n";
        $text .= "Komentar: \"{$this->comment->message}\"\n";
        $text .= "\nBalas di: {$url}";
        $text .= "\n\n— SIPMAS, Sistem Pengaduan Masyarakat";

        return $text;
    }
}