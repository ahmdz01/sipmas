<?php

namespace App\Notifications;

use App\Models\Complaint;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewComplaintSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Complaint $complaint
    ) {
    }

    // WhatsApp duluan, baru email (lihat alasan di ComplaintStatusUpdated)
    public function via($notifiable): array
    {
        return [FonnteChannel::class, 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pengaduan Baru Masuk - ' . $this->complaint->complaint_number)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Ada pengaduan baru dari warga yang perlu diverifikasi.')
            ->line('**Nomor Tiket:** ' . $this->complaint->complaint_number)
            ->line('**Judul:** ' . $this->complaint->title)
            ->line('**Kategori:** ' . $this->complaint->category->name)
            ->line('**Pelapor:** ' . $this->complaint->user->name)
            ->line('**Lokasi:** ' . $this->complaint->location_name)
            ->action('Lihat & Verifikasi Pengaduan', route('admin.complaints.show', $this->complaint->id))
            ->line('Silakan segera diverifikasi.');
    }

    public function toFonnte($notifiable): string
    {
        $text = "Halo {$notifiable->name},\n\n";
        $text .= "📢 Ada pengaduan baru masuk:\n";
        $text .= "No. Tiket: {$this->complaint->complaint_number}\n";
        $text .= "Judul: {$this->complaint->title}\n";
        $text .= "Kategori: {$this->complaint->category->name}\n";
        $text .= "Pelapor: {$this->complaint->user->name}\n";
        $text .= "Lokasi: {$this->complaint->location_name}\n";
        $text .= "\nVerifikasi di: " . route('admin.complaints.show', $this->complaint->id);
        $text .= "\n\n— SIPMAS, Sistem Pengaduan Masyarakat";

        return $text;
    }
}