@extends('admin.layouts.app')
@section('title', 'Edit Meja')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.meja.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Meja</h2>
            <p class="text-gray-600">Ubah data meja</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm max-w-lg">
    <form action="{{ route('admin.meja.update', $meja) }}" method="POST" class="p-6 space-y-4" data-unsaved="true" oninput="this.dataset.dirty='true'" onsubmit="this.dataset.dirty='false'">
        @csrf
        @method('PATCH')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Meja <span class="text-red-500">*</span></label>
            <input type="text" name="nomor_meja" value="{{ old('nomor_meja', $meja->nomor_meja) }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nomor_meja') border-red-500 @enderror" required>
            @error('nomor_meja') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas</label>
            <input type="number" name="kapasitas" value="{{ old('kapasitas', $meja->kapasitas) }}" min="1" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
            @error('kapasitas') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Area</label>
            <select name="area" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                <option value="indoor" {{ old('area', $meja->area) == 'indoor' ? 'selected' : '' }}>Indoor</option>
                <option value="outdoor" {{ old('area', $meja->area) == 'outdoor' ? 'selected' : '' }}>Outdoor</option>
            </select>
            @error('area') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                <option value="tersedia" {{ old('status', $meja->status) == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                <option value="dipakai" {{ old('status', $meja->status) == 'dipakai' ? 'selected' : '' }}>Dipakai</option>
                <option value="reserved" {{ old('status', $meja->status) == 'reserved' ? 'selected' : '' }}>Reserved</option>
                <option value="maintenance" {{ old('status', $meja->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>
            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.meja.index') }}" class="px-4 py-2 text-sm text-gray-600 border rounded-lg hover:bg-gray-50">Batal</a>
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
