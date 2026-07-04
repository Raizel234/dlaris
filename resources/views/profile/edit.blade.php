@extends('admin.layouts.app')
@section('title', 'Profil Saya')

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                <i class="fa-solid fa-user text-emerald-600"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Profil Saya</h2>
                <p class="text-sm text-gray-500">Kelola informasi profil dan akun Anda</p>
            </div>
        </div>
    </div>

    {{-- Profile Info Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-5 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-2 bg-gradient-to-r from-emerald-50 to-transparent">
            <i class="fa-solid fa-id-card text-emerald-500"></i>
            <h3 class="font-semibold text-gray-800">Informasi Profil</h3>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="p-6"
              x-data="{
                  fotoPreview: null,
                  previewFoto(event) {
                      const file = event.target.files[0];
                      if (file) {
                          const reader = new FileReader();
                          reader.onload = (e) => this.fotoPreview = e.target.result;
                          reader.readAsDataURL(file);
                      }
                  }
              }">
            @csrf
            @method('PATCH')

            {{-- Avatar --}}
            <div class="flex flex-col sm:flex-row items-start gap-6 pb-6 mb-6 border-b border-gray-100">
                <div class="relative group">
                    <div class="w-24 h-24 rounded-2xl overflow-hidden border-2 border-gray-100 flex items-center justify-center bg-gray-50"
                         :class="{ 'ring-2 ring-emerald-400': fotoPreview }">
                        <template x-if="fotoPreview">
                            <img :src="fotoPreview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!fotoPreview">
                            @if(Auth::user()->foto)
                                <img src="{{ asset('storage/' . Auth::user()->foto) }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-3xl font-bold text-gray-400">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            @endif
                        </template>
                    </div>
                    <label class="absolute inset-0 flex items-center justify-center bg-black/40 rounded-2xl opacity-0 group-hover:opacity-100 cursor-pointer transition-all">
                        <i class="fa-solid fa-camera text-white text-lg"></i>
                        <input type="file" name="foto" accept="image/*" @change="previewFoto" class="hidden">
                    </label>
                </div>
                <div class="flex-1 min-w-0 pt-1">
                    <h4 class="text-lg font-bold text-gray-800">{{ Auth::user()->name }}</h4>
                    <p class="text-sm text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                    <p class="text-sm text-gray-400 mt-0.5">{{ Auth::user()->email }}</p>
                    <p class="text-xs text-gray-400 mt-1">Klik avatar untuk mengganti foto</p>
                </div>
            </div>

            {{-- Fields --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all"
                           required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all"
                           required>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor HP</label>
                    <input type="text" name="nomor_hp" value="{{ old('nomor_hp', $user->nomor_hp ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all">
                    @error('nomor_hp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Role</label>
                    <input type="text" value="{{ ucfirst(Auth::user()->role) }}"
                           class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm bg-gray-50 text-gray-500 cursor-not-allowed"
                           disabled>
                </div>
            </div>

            <div class="flex justify-end pt-5 mt-5 border-t border-gray-100">
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-all flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-save"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- Ubah Password --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-5 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-2 bg-gradient-to-r from-blue-50 to-transparent">
            <i class="fa-solid fa-lock text-blue-500"></i>
            <h3 class="font-semibold text-gray-800">Ubah Password</h3>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Saat Ini</label>
                    <input type="password" name="current_password" required
                           class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all">
                    @error('current_password', 'updatePassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div></div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                    <input type="password" name="password" required
                           class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all">
                    @error('password', 'updatePassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all">
                </div>
            </div>

            <div class="flex justify-end pt-5 mt-5 border-t border-gray-100">
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-key"></i>
                    Perbarui Password
                </button>
            </div>
        </form>
    </div>

    {{-- Hapus Akun --}}
    <div class="bg-white rounded-xl shadow-sm border border-red-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-red-100 flex items-center gap-2 bg-gradient-to-r from-red-50 to-transparent">
            <i class="fa-solid fa-trash-can text-red-500"></i>
            <h3 class="font-semibold text-gray-800">Hapus Akun</h3>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-600 mb-4">
                Setelah akun dihapus, semua data akan dihapus secara permanen. Pastikan Anda telah menyimpan data penting sebelum melanjutkan.
            </p>
            <div x-data="{ confirmDelete: false }">
                <button @click="confirmDelete = true" type="button"
                        class="px-5 py-2.5 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-trash-can"></i>
                    Hapus Akun
                </button>

                <div x-show="confirmDelete" x-cloak class="mt-4 p-4 bg-red-50 rounded-lg border border-red-200">
                    <p class="text-sm font-medium text-red-700 mb-3">Konfirmasi Hapus Akun</p>
                    <form method="POST" action="{{ route('profile.destroy') }}">
                        @csrf
                        @method('DELETE')
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-red-700 mb-1">Masukkan password untuk konfirmasi</label>
                            <input type="password" name="password" required placeholder="Password saat ini"
                                   class="w-full border border-red-300 rounded-lg px-3.5 py-2.5 text-sm focus:border-red-500 focus:ring-2 focus:ring-red-500/20 outline-none transition-all">
                            @error('password', 'userDeletion') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex gap-2">
                            <button type="button" @click="confirmDelete = false"
                                    class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-all">
                                Ya, Hapus Akun
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
