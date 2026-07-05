<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintRating;
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
            'avgRating'   => round(ComplaintRating::avg('rating') ?? 0, 1),
            'totalRatings' => ComplaintRating::count(),
        ];

        // 5 pengaduan terbaru
        $latestComplaints = Complaint::with(['category', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // Pengaduan per kategori (untuk chart)
        $byCategory = Category::withCount('complaints')->get();

        // Pengaduan per bulan (tahun berjalan) - semua bulan diisi, kosong = 0
        $monthlyRaw = Complaint::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthNames = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $byMonth = collect(range(1, 12))->map(function ($m) use ($monthlyRaw, $monthNames) {
            return [
                'month' => $monthNames[$m],
                'total' => (int) ($monthlyRaw[$m] ?? 0),
            ];
        });

        return view('admin.dashboard', compact('stats', 'latestComplaints', 'byCategory', 'byMonth'));
    }
}