@extends('admin.layouts.app')
@section('title', 'Edit Karyawan')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.karyawan.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Karyawan</h2>
            <p class="text-gray-600">Ubah data karyawan</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm max-w-lg">
    <form action="{{ route('admin.karyawan.update', $karyawan) }}" method="POST" class="p-6 space-y-4" data-unsaved="true" oninput="this.dataset.dirty='true'" onsubmit="this.dataset.dirty='false'">
        @csrf
        @method('PATCH')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" name="nama" value="{{ old('nama', $karyawan->nama) }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama') border-red-500 @enderror" required>
            @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan <span class="text-red-500">*</span></label>
            <select name="jabatan" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('jabatan') border-red-500 @enderror" required>
                <option value="">Pilih Jabatan</option>
                <option value="admin" {{ old('jabatan', $karyawan->jabatan) == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="kasir" {{ old('jabatan', $karyawan->jabatan) == 'kasir' ? 'selected' : '' }}>Kasir</option>
                <option value="karyawan" {{ old('jabatan', $karyawan->jabatan) == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
            </select>
            @error('jabatan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
            <input type="text" name="nomor_hp" value="{{ old('nomor_hp', $karyawan->nomor_hp) }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
            @error('nomor_hp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
            <input type="email" name="email" value="{{ old('email', optional($karyawan->user)->email) }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror" required>
            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-gray-400 text-xs">(Kosongkan jika tidak diubah)</span></label>
            <input type="password" name="password" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.karyawan.index') }}" class="px-4 py-2 text-sm text-gray-600 border rounded-lg hover:bg-gray-50">Batal</a>
            <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700 flex items-center gap-2"><i class="fa-solid fa-save"></i> Update</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
window.addEventListener('beforeunload', function(e) {
    const form = document.querySelector('form[data-unsaved="true"][data-dirty="true"]');
    if (form) { e.preventDefault(); e.returnValue = ''; }
});
</script>
@endpush
