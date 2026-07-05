<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Daftar semua kategori + jumlah pengaduan per kategori
    public function index()
    {
        $categories = Category::withCount('complaints')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    // Form tambah kategori
    public function create()
    {
        return view('admin.categories.create');
    }

    // Simpan kategori baru
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:100|unique:categories,name',
            'icon'  => 'required|string|max:50',
            'color' => 'required|string|max:7',
        ], [
            'name.required'  => 'Nama kategori wajib diisi.',
            'name.unique'    => 'Nama kategori ini sudah ada.',
            'icon.required'  => 'Icon wajib dipilih.',
            'color.required' => 'Warna wajib dipilih.',
        ]);

        Category::create($request->only(['name', 'icon', 'color']));

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori "' . $request->name . '" berhasil ditambahkan.');
    }

    // Form edit kategori
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    // Update kategori
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'  => 'required|string|max:100|unique:categories,name,' . $category->id,
            'icon'  => 'required|string|max:50',
            'color' => 'required|string|max:7',
        ], [
            'name.required'  => 'Nama kategori wajib diisi.',
            'name.unique'    => 'Nama kategori ini sudah ada.',
            'icon.required'  => 'Icon wajib dipilih.',
            'color.required' => 'Warna wajib dipilih.',
        ]);

        $category->update($request->only(['name', 'icon', 'color']));

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori "' . $category->name . '" berhasil diperbarui.');
    }

    // Hapus kategori — hanya jika belum dipakai oleh pengaduan manapun
    public function destroy(Category $category)
    {
        $count = $category->complaints()->count();

        if ($count > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', "Kategori \"{$category->name}\" tidak bisa dihapus karena masih dipakai oleh {$count} pengaduan.");
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}