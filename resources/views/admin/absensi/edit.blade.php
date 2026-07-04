@extends('admin.layouts.app')
@section('title', 'Edit Absensi')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.absensi.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Absensi</h2>
            <p class="text-gray-600">Ubah data absensi</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm max-w-2xl">
    <form action="{{ route('admin.absensi.update', $absensi) }}" method="POST" class="p-6 space-y-4" data-unsaved="true" oninput="this.dataset.dirty='true'" onsubmit="this.dataset.dirty='false'">
        @csrf
        @method('PATCH')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Karyawan</label>
                <input type="text" value="{{ $absensi->user->name ?? '-' }}" class="w-full border rounded-lg px-3 py-2 text-sm bg-gray-50 border-gray-300" readonly>
                <input type="hidden" name="user_id" value="{{ $absensi->user_id }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="text" value="{{ $absensi->tanggal->format('d/m/Y') }}" class="w-full border rounded-lg px-3 py-2 text-sm bg-gray-50 border-gray-300" readonly>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Masuk</label>
                <input type="time" name="jam_masuk" value="{{ old('jam_masuk', $absensi->jam_masuk ? \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') : '') }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                @error('jam_masuk') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Pulang</label>
                <input type="time" name="jam_pulang" value="{{ old('jam_pulang', $absensi->jam_pulang ? \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i') : '') }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                @error('jam_pulang') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror" required>
                    <option value="hadir" {{ old('status', $absensi->status) == 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="izin" {{ old('status', $absensi->status) == 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="sakit" {{ old('status', $absensi->status) == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="alpha" {{ old('status', $absensi->status) == 'alpha' ? 'selected' : '' }}>Alpha</option>
                    <option value="cuti" {{ old('status', $absensi->status) == 'cuti' ? 'selected' : '' }}>Cuti</option>
                </select>
                @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">{{ old('keterangan', $absensi->keterangan) }}</textarea>
                @error('keterangan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('admin.absensi.index') }}" class="px-4 py-2 text-sm text-gray-600 border rounded-lg hover:bg-gray-50">Batal</a>
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
