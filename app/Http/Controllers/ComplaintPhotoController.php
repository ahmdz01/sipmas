<?php

namespace App\Http\Controllers;

use App\Models\ComplaintPhoto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ComplaintPhotoController extends Controller
{
    // Hapus satu foto dari galeri (hanya pemilik & selama masih pending)
    public function destroy(ComplaintPhoto $photo)
    {
        $complaint = $photo->complaint;

        if ($complaint->user_id !== Auth::id() || $complaint->status !== 'pending') {
            abort(403);
        }

        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }
}