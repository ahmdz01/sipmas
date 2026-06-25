<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Category;
use Illuminate\Http\Request;

class MapController extends Controller
{
    // Peta publik (tanpa login)
    public function public()
    {
        $categories = Category::all();
        return view('map.public', compact('categories'));
    }

    // Peta khusus admin
    public function admin()
    {
        $categories = Category::all();
        $stats = [
            'total'       => Complaint::count(),
            'pending'     => Complaint::where('status', 'pending')->count(),
            'in_progress' => Complaint::where('status', 'in_progress')->count(),
            'resolved'    => Complaint::where('status', 'resolved')->count(),
        ];
        return view('map.admin', compact('categories', 'stats'));
    }

    // API: return semua pengaduan dalam format GeoJSON untuk Leaflet
    public function geojson(Request $request)
    {
        $query = Complaint::with(['category', 'user'])
            ->whereNotIn('status', ['rejected']);

        // Filter by kategori jika ada
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status jika ada
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $complaints = $query->get();

        // Format GeoJSON
        $features = $complaints->map(function ($complaint) {
            return [
                'type' => 'Feature',
                'geometry' => [
                    'type'        => 'Point',
                    'coordinates' => [$complaint->longitude, $complaint->latitude],
                ],
                'properties' => [
                    'id'             => $complaint->id,
                    'complaint_number' => $complaint->complaint_number,
                    'title'          => $complaint->title,
                    'category'       => $complaint->category->name,
                    'category_color' => $complaint->category->color,
                    'status'         => $complaint->status,
                    'status_label'   => $complaint->statusBadge()['label'],
                    'location_name'  => $complaint->location_name,
                    'reporter'       => $complaint->user->name,
                    'created_at'     => $complaint->created_at->format('d M Y'),
                    'photo'          => $complaint->photo ? asset('storage/' . $complaint->photo) : null,
                    'url'            => route('complaints.show', $complaint->id),
                ],
            ];
        });

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}