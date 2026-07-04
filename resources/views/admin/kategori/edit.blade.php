@extends('admin.layouts.app')
@section('title', 'Edit Kategori')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.kategori.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Kategori</h2>
            <p class="text-gray-600">Ubah data kategori</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm max-w-lg">
    <form action="{{ route('admin.kategori.update', $kategori) }}" method="POST" class="p-6 space-y-4" data-unsaved="true" oninput="this.dataset.dirty='true'" onsubmit="this.dataset.dirty='false'">
        @csrf
        @method('PATCH')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori <span class="text-red-500">*</span></label>
            <input type="text" name="nama" value="{{ old('nama', $kategori->nama) }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama') border-red-500 @enderror" required>
            @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ikon (Font Awesome)</label>
            <input type="text" name="ikon" value="{{ old('ikon', $kategori->ikon) }}" placeholder="contoh: fa-solid fa-coffee" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
            <p class="text-xs text-gray-400 mt-1">Gunakan class Font Awesome, misal: fa-solid fa-utensils</p>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $kategori->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <label class="text-sm font-medium text-gray-700">Aktif</label>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.kategori.index') }}" class="px-4 py-2 text-sm text-gray-600 border rounded-lg hover:bg-gray-50">Batal</a>
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
