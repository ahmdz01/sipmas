<p class="text-sm text-gray-500 mb-4">
    Setelah akun dihapus, seluruh data dan riwayat pengaduan kamu akan dihapus permanen dan tidak bisa dikembalikan.
    Pastikan sudah menyimpan data yang diperlukan sebelum melanjutkan.
</p>

<button
    x-data=""
    x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    class="bg-red-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-red-700 flex items-center gap-2">
    <i class="fas fa-trash"></i> Hapus Akun
</button>

<x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
    <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
        @csrf
        @method('delete')

        <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
            <i class="fas fa-exclamation-triangle text-red-500"></i>
            Yakin ingin menghapus akun?
        </h2>

        <p class="mt-2 text-sm text-gray-600">
            Semua data akan dihapus permanen. Masukkan password kamu untuk konfirmasi.
        </p>

        <div class="mt-4">
            <x-input-label for="password" value="Password" class="sr-only" />

            <x-text-input
                id="password"
                name="password"
                type="password"
                class="mt-1 block w-full rounded-lg"
                placeholder="Password"
            />

            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
        </div>

        <div class="mt-6 flex justify-end gap-2">
            <button type="button" x-on:click="$dispatch('close')"
                class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-300">
                Batal
            </button>

            <button type="submit"
                class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-700">
                <i class="fas fa-trash mr-1"></i> Hapus Akun
            </button>
        </div>
    </form>
</x-modal>