@extends('layouts.admin')

@section('title', 'Tambah Kategori')

@section('content')

<div class="max-w-lg bg-white rounded-lg shadow p-6">
    <h2 class="font-semibold text-gray-700 mb-5">Tambah Kategori Baru</h2>

    <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   placeholder="Contoh: Penerangan Jalan"
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                          {{ $errors->has('name') ? 'border-red-400' : 'border-gray-300' }}">
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Icon (nama icon Font Awesome, tanpa "fa-")
            </label>
            <input type="text" name="icon" id="icon-input" value="{{ old('icon') }}"
                   placeholder="Contoh: lightbulb, water, tree"
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                          {{ $errors->has('icon') ? 'border-red-400' : 'border-gray-300' }}">
            <p class="text-xs text-gray-400 mt-1">
                Cari nama icon di <a href="https://fontawesome.com/search?ic=free" target="_blank" class="text-blue-600 underline">fontawesome.com</a>, tulis tanpa "fa-".
            </p>
            @error('icon')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Warna</label>
            <div class="flex items-center gap-3">
                <input type="color" name="color" id="color-input" value="{{ old('color', '#3498db') }}"
                       class="w-12 h-10 border rounded cursor-pointer">
                <input type="text" id="color-text" value="{{ old('color', '#3498db') }}"
                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono
                              focus:outline-none focus:ring-2 focus:ring-blue-500" readonly>
            </div>
            @error('color')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="border rounded-lg p-4 flex items-center gap-3 bg-gray-50">
            <span class="text-xs text-gray-500">Preview:</span>
            <span id="preview-badge" class="w-10 h-10 rounded-full flex items-center justify-center"
                  style="background-color: #3498db20; color: #3498db;">
                <i id="preview-icon" class="fas fa-tag"></i>
            </span>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="bg-blue-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-blue-700">
                <i class="fas fa-save mr-1"></i> Simpan
            </button>
            <a href="{{ route('admin.categories.index') }}"
               class="text-gray-500 text-sm hover:text-gray-700">Batal</a>
        </div>
    </form>
</div>

<script>
    const iconInput  = document.getElementById('icon-input');
    const colorInput = document.getElementById('color-input');
    const colorText  = document.getElementById('color-text');
    const previewIcon  = document.getElementById('preview-icon');
    const previewBadge = document.getElementById('preview-badge');

    function updatePreview() {
        const icon  = iconInput.value.trim() || 'tag';
        const color = colorInput.value;
        previewIcon.className = 'fas fa-' + icon;
        previewBadge.style.backgroundColor = color + '20';
        previewBadge.style.color = color;
        colorText.value = color;
    }

    iconInput.addEventListener('input', updatePreview);
    colorInput.addEventListener('input', updatePreview);
    updatePreview();
</script>

@endsection