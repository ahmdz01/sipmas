<p class="text-sm text-gray-500 mb-5">
    Gunakan password yang panjang dan acak supaya akun kamu tetap aman.
</p>

<form method="post" action="{{ route('password.update') }}" class="space-y-5">
    @csrf
    @method('put')

    <div>
        <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 mb-1">
            Password Saat Ini
        </label>
        <x-text-input id="update_password_current_password" name="current_password" type="password"
            class="mt-1 block w-full rounded-lg" autocomplete="current-password" />
        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
    </div>

    <div>
        <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-1">
            Password Baru
        </label>
        <x-text-input id="update_password_password" name="password" type="password"
            class="mt-1 block w-full rounded-lg" autocomplete="new-password" />
        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
    </div>

    <div>
        <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
            Konfirmasi Password Baru
        </label>
        <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
            class="mt-1 block w-full rounded-lg" autocomplete="new-password" />
        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
    </div>

    <div class="flex items-center gap-4 pt-2">
        <button type="submit"
            class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 flex items-center gap-2">
            <i class="fas fa-key"></i> Ubah Password
        </button>

        @if (session('status') === 'password-updated')
            <p x-data="{ show: true }" x-show="show" x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-green-600 flex items-center gap-1">
                <i class="fas fa-check-circle"></i> Tersimpan.
            </p>
        @endif
    </div>
</form>