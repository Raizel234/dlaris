@extends('admin.layouts.app')
@section('title', 'Edit Ruangan')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.ruangan.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Ruangan</h2>
            <p class="text-gray-600">Ubah data ruangan karaoke</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm max-w-2xl">
    <form action="{{ route('admin.ruangan.update', $ruangan) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4" data-unsaved="true" oninput="this.dataset.dirty='true'" onsubmit="this.dataset.dirty='false'">
        @csrf
        @method('PATCH')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ruangan <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $ruangan->nama) }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama') border-red-500 @enderror" required>
                @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas</label>
                <input type="number" name="kapasitas" value="{{ old('kapasitas', $ruangan->kapasitas) }}" min="1" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                @error('kapasitas') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tarif / Jam <span class="text-red-500">*</span></label>
                <input type="number" name="tarif_per_jam" value="{{ old('tarif_per_jam', $ruangan->tarif_per_jam) }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tarif_per_jam') border-red-500 @enderror" required>
                @error('tarif_per_jam') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fasilitas</label>
                <textarea name="fasilitas" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" placeholder="Pisahkan dengan koma">{{ old('fasilitas', $ruangan->fasilitas) }}</textarea>
                @error('fasilitas') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Ruangan</label>
                @if($ruangan->foto)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $ruangan->foto) }}" class="w-32 h-32 object-cover rounded-lg border">
                    </div>
                @endif
                <input type="file" name="foto" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                @error('foto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                    <option value="tersedia" {{ old('status', $ruangan->status) == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="dipakai" {{ old('status', $ruangan->status) == 'dipakai' ? 'selected' : '' }}>Dipakai</option>
                    <option value="maintenance" {{ old('status', $ruangan->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
                @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('admin.ruangan.index') }}" class="px-4 py-2 text-sm text-gray-600 border rounded-lg hover:bg-gray-50">Batal</a>
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
