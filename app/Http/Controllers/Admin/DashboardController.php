<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use App\Models\Category;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total'       => Complaint::count(),
            'pending'     => Complaint::where('status', 'pending')->count(),
            'verified'    => Complaint::where('status', 'verified')->count(),
            'in_progress' => Complaint::where('status', 'in_progress')->count(),
            'resolved'    => Complaint::where('status', 'resolved')->count(),
            'rejected'    => Complaint::where('status', 'rejected')->count(),
            'users'       => User::where('role', 'masyarakat')->count(),
        ];

        // 5 pengaduan terbaru
        $latestComplaints = Complaint::with(['category', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // Pengaduan per kategori (untuk chart)
        $byCategory = Category::withCount('complaints')->get();

        // Pengaduan per bulan (6 bulan terakhir)
        $byMonth = Complaint::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.dashboard', compact('stats', 'latestComplaints', 'byCategory', 'byMonth'));
    }
}