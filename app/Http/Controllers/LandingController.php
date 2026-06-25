<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Category;
use App\Models\User;

class LandingController extends Controller
{
    public function index()
    {
        $stats = [
            'total'    => Complaint::count(),
            'resolved' => Complaint::where('status', 'resolved')->count(),
            'pending'  => Complaint::where('status', 'pending')->count(),
            'users'    => User::where('role', 'masyarakat')->count(),
        ];

        $categories = Category::withCount('complaints')->get();

        $latestComplaints = Complaint::with(['category', 'user'])
            ->whereNotIn('status', ['rejected'])
            ->latest()
            ->take(4)
            ->get();

        return view('landing', compact('stats', 'categories', 'latestComplaints'));
    }
}