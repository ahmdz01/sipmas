<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteChannel
{
    public function send($notifiable, Notification $notification)
    {
        // Notifikasi wajib punya method toFonnte()
        if (!method_exists($notification, 'toFonnte')) {
            return;
        }

        $phone = $notifiable->phone ?? null;

        // Lewati kalau user tidak punya nomor HP
        if (!$phone) {
            return;
        }

        $message = $notification->toFonnte($notifiable);

        $response = Http::withHeaders([
            'Authorization' => config('services.fonnte.token'),
        ])->post('https://api.fonnte.com/send', [
            'target'  => $this->formatPhone($phone),
            'message' => $message,
        ]);

        // Catat ke log kalau gagal, supaya tidak mengganggu proses utama
        if (!$response->successful()) {
            Log::warning('Gagal mengirim WhatsApp via Fonnte', [
                'phone'    => $phone,
                'response' => $response->body(),
            ]);
        }
    }

    // Ubah nomor lokal (08xx) jadi format internasional (628xx) yang dibutuhkan Fonnte
    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone); // buang karakter non-angka

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }
}