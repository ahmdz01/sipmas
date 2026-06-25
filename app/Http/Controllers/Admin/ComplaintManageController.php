<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintUpdate;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintManageController extends Controller
{
    // Daftar semua pengaduan dengan filter
    public function index(Request $request)
    {
        $query = Complaint::with(['category', 'user'])->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('complaint_number', 'like', '%' . $request->search . '%')
                  ->orWhere('location_name', 'like', '%' . $request->search . '%');
            });
        }

        $complaints = $query->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('admin.complaints.index', compact('complaints', 'categories'));
    }

    // Detail pengaduan di sisi admin
    public function show(Complaint $complaint)
    {
        $complaint->load(['category', 'user', 'updates.user', 'handler']);
        return view('admin.complaints.show', compact('complaint'));
    }

    // Update status pengaduan
    public function updateStatus(Request $request, Complaint $complaint)
    {
        $request->validate([
            'status' => 'required|in:verified,in_progress,resolved,rejected',
            'note'   => 'nullable|string|max:500',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:500',
        ], [
            'status.required'            => 'Status wajib dipilih.',
            'rejection_reason.required_if' => 'Alasan penolakan wajib diisi jika ditolak.',
        ]);

        $updateData = [
            'status'     => $request->status,
            'handled_by' => Auth::id(),
        ];

        if ($request->status === 'rejected') {
            $updateData['rejection_reason'] = $request->rejection_reason;
        }

        if ($request->status === 'verified') {
            $updateData['verified_at'] = now();
        }

        if ($request->status === 'resolved') {
            $updateData['resolved_at'] = now();
        }

        $complaint->update($updateData);

        // Catat riwayat update
        ComplaintUpdate::create([
            'complaint_id' => $complaint->id,
            'user_id'      => Auth::id(),
            'status'       => $request->status,
            'note'         => $request->note ?? 'Status diperbarui oleh admin.',
        ]);

        return redirect()->route('admin.complaints.show', $complaint->id)
            ->with('success', 'Status pengaduan berhasil diperbarui.');
    }
}