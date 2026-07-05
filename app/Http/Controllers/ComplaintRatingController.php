<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintRatingController extends Controller
{
    // Simpan rating & ulasan untuk pengaduan yang sudah selesai
    public function store(Request $request, Complaint $complaint)
    {
        // Hanya pemilik pengaduan yang boleh memberi rating
        if ($complaint->user_id !== Auth::id()) {
            abort(403);
        }

        // Hanya pengaduan yang sudah selesai yang bisa dinilai
        if ($complaint->status !== 'resolved') {
            abort(403, 'Pengaduan belum selesai, belum bisa diberi rating.');
        }

        // Cegah rating ganda
        if ($complaint->rating) {
            return redirect()->route('complaints.show', $complaint->id)
                ->with('error', 'Pengaduan ini sudah pernah diberi rating.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500',
        ], [
            'rating.required' => 'Silakan pilih rating bintang.',
            'rating.min'      => 'Rating minimal 1 bintang.',
            'rating.max'      => 'Rating maksimal 5 bintang.',
        ]);

        ComplaintRating::create([
            'complaint_id' => $complaint->id,
            'rating'       => $request->rating,
            'review'       => $request->review,
        ]);

        return redirect()->route('complaints.show', $complaint->id)
            ->with('success', 'Terima kasih atas penilaian Anda!');
    }
}