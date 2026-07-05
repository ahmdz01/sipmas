<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintComment;
use App\Models\User;
use App\Notifications\NewComplaintComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ComplaintCommentController extends Controller
{
    public function store(Request $request, Complaint $complaint)
    {
        $isOwner = $complaint->user_id === Auth::id();
        $isAdmin = Auth::user()->isAdmin();

        // Cuma pemilik pengaduan atau admin yang boleh komentar
        if (!$isOwner && !$isAdmin) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string|max:1000',
        ], [
            'message.required' => 'Komentar tidak boleh kosong.',
        ]);

        $comment = ComplaintComment::create([
            'complaint_id' => $complaint->id,
            'user_id'      => Auth::id(),
            'message'      => $request->message,
        ]);

        // Notifikasi ke pihak lain (bukan ke diri sendiri)
        if ($isAdmin) {
            $complaint->user->notify(new NewComplaintComment($complaint, $comment));
        } else {
            $admins = User::where('role', 'admin')->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new NewComplaintComment($complaint, $comment));
            }
        }

        return back()->with('success', 'Komentar berhasil dikirim.');
    }
}