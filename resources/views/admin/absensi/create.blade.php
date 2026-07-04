@extends('admin.layouts.app')
@section('title', 'Tambah Absensi')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.absensi.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tambah Absensi</h2>
            <p class="text-gray-600">Catat absensi karyawan</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm max-w-2xl">
    <form action="{{ route('admin.absensi.store') }}" method="POST" class="p-6 space-y-4" data-unsaved="true" oninput="this.dataset.dirty='true'" onsubmit="this.dataset.dirty='false'">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Karyawan <span class="text-red-500">*</span></label>
                <select name="user_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('user_id') border-red-500 @enderror" required>
                    <option value="">Pilih Karyawan</option>
                    @foreach($karyawans as $k)
                        <option value="{{ $k->id }}" {{ old('user_id') == $k->id ? 'selected' : '' }}>{{ $k->name }}</option>
                    @endforeach
                </select>
                @error('user_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tanggal') border-red-500 @enderror" required>
                @error('tanggal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Masuk</label>
                <input type="time" name="jam_masuk" value="{{ old('jam_masuk') }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                @error('jam_masuk') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Pulang</label>
                <input type="time" name="jam_pulang" value="{{ old('jam_pulang') }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                @error('jam_pulang') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror" required>
                    <option value="">Pilih Status</option>
                    <option value="hadir" {{ old('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="izin" {{ old('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="sakit" {{ old('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="alpha" {{ old('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                    <option value="cuti" {{ old('status') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                </select>
                @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">{{ old('keterangan') }}</textarea>
                @error('keterangan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('admin.absensi.index') }}" class="px-4 py-2 text-sm text-gray-600 border rounded-lg hover:bg-gray-50">Batal</a>
            <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700 flex items-center gap-2"><i class="fa-solid fa-save"></i> Simpan</button>
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
