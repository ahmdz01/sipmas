<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintUpdate;
use App\Models\Category;
use App\Notifications\ComplaintStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ComplaintManageController extends Controller
{
    // Daftar semua pengaduan dengan filter
    public function index(Request $request)
    {
        $query = $this->applyFilters(Complaint::with(['category', 'user']), $request)->latest();

        $complaints = $query->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('admin.complaints.index', compact('complaints', 'categories'));
    }

    // Detail pengaduan di sisi admin
    public function show(Complaint $complaint)
    {
        $complaint->load(['category', 'user', 'updates.user', 'handler', 'rating']);
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

        ComplaintUpdate::create([
            'complaint_id' => $complaint->id,
            'user_id'      => Auth::id(),
            'status'       => $request->status,
            'note'         => $request->note ?? 'Status diperbarui oleh admin.',
        ]);

        $complaint->user->notify(new ComplaintStatusUpdated($complaint, $request->note));

        return redirect()->route('admin.complaints.show', $complaint->id)
            ->with('success', 'Status pengaduan berhasil diperbarui dan notifikasi email telah dikirim.');
    }

    // Export laporan ke CSV (bisa dibuka di Excel)
    public function exportCsv(Request $request)
    {
        $complaints = $this->applyFilters(Complaint::with(['category', 'user']), $request)
            ->latest()
            ->get();

        $filename = 'laporan-pengaduan-' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->streamDownload(function () use ($complaints) {
            $handle = fopen('php://output', 'w');

            // BOM supaya Excel membaca karakter UTF-8 (misal huruf é, ñ, dll) dengan benar
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No. Tiket', 'Judul', 'Kategori', 'Pelapor', 'Lokasi',
                'Status', 'Tanggal Lapor', 'Tanggal Selesai',
            ]);

            foreach ($complaints as $c) {
                fputcsv($handle, [
                    $c->complaint_number,
                    $c->title,
                    $c->category->name,
                    $c->user->name,
                    $c->location_name,
                    $c->statusBadge()['label'],
                    $c->created_at->format('d-m-Y H:i'),
                    $c->resolved_at ? $c->resolved_at->format('d-m-Y H:i') : '-',
                ]);
            }

            fclose($handle);
        }, $filename, $headers);
    }

    // Export laporan ke PDF
    public function exportPdf(Request $request)
    {
        $complaints = $this->applyFilters(Complaint::with(['category', 'user']), $request)
            ->latest()
            ->get();

        $filters = [
            'status'   => $request->status,
            'search'   => $request->search,
            'category' => $request->category_id
                ? Category::find($request->category_id)?->name
                : null,
        ];

        $pdf = Pdf::loadView('admin.complaints.export-pdf', compact('complaints', 'filters'))
            ->setPaper('a4', 'landscape');

        $filename = 'laporan-pengaduan-' . now()->format('Y-m-d_His') . '.pdf';

        return $pdf->download($filename);
    }

    // Helper: terapkan filter status/kategori/search ke query (dipakai index & export)
    private function applyFilters($query, Request $request)
    {
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

        return $query;
    }
}