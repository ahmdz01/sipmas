<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\ComplaintUpdate;
use App\Models\User;
use App\Notifications\NewComplaintSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class ComplaintController extends Controller
{

    public function __construct()
    {
        // Batasi maksimal 6 percobaan submit per menit — mencegah spam/bot
        $this->middleware('throttle:6,1')->only(['store']);
    }

    // Daftar pengaduan (route: complaints.index) — arahkan ke dashboard
    public function index()
    {
        return redirect()->route('dashboard');
    }

    // Dashboard user: lihat pengaduan milik sendiri + filter & search
    public function dashboard(Request $request)
    {
        $query = Complaint::with('category')
            ->where('user_id', Auth::id())
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('complaint_number', 'like', '%' . $request->search . '%')
                    ->orWhere('location_name', 'like', '%' . $request->search . '%');
            });
        }

        $complaints = $query->paginate(10)->withQueryString();

        $stats = [
            'total'       => Complaint::where('user_id', Auth::id())->count(),
            'pending'     => Complaint::where('user_id', Auth::id())->where('status', 'pending')->count(),
            'in_progress' => Complaint::where('user_id', Auth::id())->where('status', 'in_progress')->count(),
            'resolved'    => Complaint::where('user_id', Auth::id())->where('status', 'resolved')->count(),
        ];

        return view('dashboard', compact('complaints', 'stats'));
    }

    // Form buat pengaduan baru
    public function create()
    {
        $categories = Category::all();
        return view('complaints.create', compact('categories'));
    }

    // Simpan pengaduan baru
    public function store(Request $request)
    {

        $todayCount = Complaint::where('user_id', Auth::id())
            ->whereDate('created_at', now())
            ->count();

        if ($todayCount >= 5) {
            return back()->with('error', 'Anda sudah mengirim 5 pengaduan hari ini. Silakan coba lagi besok.');
        }

        $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'title'         => 'required|string|max:255',
            'description'   => 'required|string|min:20',
            'location_name' => 'required|string|max:255',
            'latitude'      => 'required|numeric|between:-90,90',
            'longitude'     => 'required|numeric|between:-180,180',
            'photo'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'category_id.required'   => 'Kategori wajib dipilih.',
            'title.required'         => 'Judul pengaduan wajib diisi.',
            'description.required'   => 'Deskripsi wajib diisi.',
            'description.min'        => 'Deskripsi minimal 20 karakter.',
            'location_name.required' => 'Nama lokasi wajib diisi.',
            'latitude.required'      => 'Lokasi di peta wajib ditandai.',
            'longitude.required'     => 'Lokasi di peta wajib ditandai.',
            'photo.image'            => 'File harus berupa gambar.',
            'photo.max'              => 'Ukuran foto maksimal 2MB.',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('complaints', 'public');
        }

        $complaint = Complaint::create([
            'complaint_number' => Complaint::generateNumber(),
            'user_id'          => Auth::id(),
            'category_id'      => $request->category_id,
            'title'            => $request->title,
            'description'      => $request->description,
            'location_name'    => $request->location_name,
            'latitude'         => $request->latitude,
            'longitude'        => $request->longitude,
            'photo'            => $photoPath,
            'status'           => 'pending',
        ]);

        // Catat riwayat awal
        ComplaintUpdate::create([
            'complaint_id' => $complaint->id,
            'user_id'      => Auth::id(),
            'status'       => 'pending',
            'note'         => 'Pengaduan berhasil dikirim dan menunggu verifikasi.',
        ]);

        // Notifikasi ke semua admin: ada pengaduan baru yang perlu diverifikasi
        $admins = User::where('role', 'admin')->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewComplaintSubmitted($complaint));
        }

        return redirect()->route('complaints.show', $complaint->id)
            ->with('success', 'Pengaduan berhasil dikirim! Nomor tiket: ' . $complaint->complaint_number);

        return redirect()->route('complaints.show', $complaint->id)
            ->with('success', 'Pengaduan berhasil dikirim! Nomor tiket: ' . $complaint->complaint_number);
    }

    // Detail pengaduan + timeline
    public function show(Complaint $complaint)
    {
        if (! Auth::user()->isAdmin() && $complaint->user_id !== Auth::id()) {
            abort(403);
        }

        $complaint->load(['category', 'user', 'updates.user', 'handler', 'rating']);
        return view('complaints.show', compact('complaint'));
    }

    // Form edit (hanya jika masih pending)
    public function edit(Complaint $complaint)
    {
        if ($complaint->user_id !== Auth::id() || $complaint->status !== 'pending') {
            abort(403, 'Pengaduan tidak dapat diedit.');
        }
        $categories = Category::all();
        return view('complaints.edit', compact('complaint', 'categories'));
    }

    // Update pengaduan
    public function update(Request $request, Complaint $complaint)
    {
        if ($complaint->user_id !== Auth::id() || $complaint->status !== 'pending') {
            abort(403);
        }

        $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'title'         => 'required|string|max:255',
            'description'   => 'required|string|min:20',
            'location_name' => 'required|string|max:255',
            'latitude'      => 'required|numeric|between:-90,90',
            'longitude'     => 'required|numeric|between:-180,180',
            'photo'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $photoPath = $complaint->photo;
        if ($request->hasFile('photo')) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            $photoPath = $request->file('photo')->store('complaints', 'public');
        }

        $complaint->update([
            'category_id'   => $request->category_id,
            'title'         => $request->title,
            'description'   => $request->description,
            'location_name' => $request->location_name,
            'latitude'      => $request->latitude,
            'longitude'     => $request->longitude,
            'photo'         => $photoPath,
        ]);

        return redirect()->route('complaints.show', $complaint->id)
            ->with('success', 'Pengaduan berhasil diperbarui.');
    }

    // Hapus pengaduan (hanya jika masih pending)
    public function destroy(Complaint $complaint)
    {
        if ($complaint->user_id !== Auth::id() || $complaint->status !== 'pending') {
            abort(403);
        }

        if ($complaint->photo) {
            Storage::disk('public')->delete($complaint->photo);
        }

        $complaint->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Pengaduan berhasil dihapus.');
    }
}
