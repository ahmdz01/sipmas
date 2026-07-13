<p class="text-sm text-gray-500 mb-5">
    Perbarui nama, email, dan nomor WhatsApp akun kamu.
</p>

<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" class="space-y-5">
    @csrf
    @method('patch')

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
        <x-text-input id="name" name="name" type="text"
            class="mt-1 block w-full rounded-lg"
            :value="old('name', $user->name)" required autofocus autocomplete="name" />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <x-text-input id="email" name="email" type="email"
            class="mt-1 block w-full rounded-lg"
            :value="old('email', $user->email)" required autocomplete="username" />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-2 bg-yellow-50 border border-yellow-200 rounded-lg px-3 py-2">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-triangle-exclamation mr-1"></i>
                    Email kamu belum diverifikasi.
                    <button form="send-verification" class="underline font-medium hover:text-yellow-900">
                        Klik untuk kirim ulang email verifikasi.
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-1 text-sm font-medium text-green-600">
                        <i class="fas fa-check mr-1"></i>Link verifikasi baru sudah dikirim ke email kamu.
                    </p>
                @endif
            </div>
        @endif
    </div>

    <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
        <x-text-input id="phone" name="phone" type="text"
            class="mt-1 block w-full rounded-lg"
            :value="old('phone', $user->phone)" required autocomplete="tel" placeholder="08xxxxxxxxxx" />
        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
    </div>

    <div class="flex items-center gap-4 pt-2">
        <button type="submit"
            class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 flex items-center gap-2">
            <i class="fas fa-save"></i> Simpan Perubahan
        </button>

        @if (session('status') === 'profile-updated')
            <p x-data="{ show: true }" x-show="show" x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-green-600 flex items-center gap-1">
                <i class="fas fa-check-circle"></i> Tersimpan.
            </p>
        @endif
    </div>
</form>