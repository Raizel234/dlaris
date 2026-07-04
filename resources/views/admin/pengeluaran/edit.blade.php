@extends('admin.layouts.app')
@section('title', 'Edit Pengeluaran')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.pengeluaran.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Pengeluaran</h2>
            <p class="text-gray-600">Ubah data pengeluaran</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm max-w-2xl">
    <form action="{{ route('admin.pengeluaran.update', $pengeluaran) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4" data-unsaved="true" oninput="this.dataset.dirty='true'" onsubmit="this.dataset.dirty='false'"
          x-data="{
              form: { kategori: '{{ old('kategori', $pengeluaran->kategori) }}', judul: '{{ old('judul', $pengeluaran->judul) }}', deskripsi: '{{ old('deskripsi', $pengeluaran->deskripsi) }}', jumlah: '{{ old('jumlah', $pengeluaran->jumlah) }}', tanggal: '{{ old('tanggal', $pengeluaran->tanggal ? $pengeluaran->tanggal->format('Y-m-d') : date('Y-m-d')) }}' },
              errors: {},
              previewUrl: null,
              existingBukti: '{{ $pengeluaran->bukti ? asset("storage/" . $pengeluaran->bukti) : "" }}',
              validate() {
                  this.errors = {};
                  if (!this.form.kategori) this.errors.kategori = 'Kategori harus dipilih';
                  if (!this.form.judul.trim()) this.errors.judul = 'Judul harus diisi';
                  if (!this.form.jumlah || this.form.jumlah <= 0) this.errors.jumlah = 'Jumlah harus diisi dengan angka positif';
                  if (!this.form.tanggal) this.errors.tanggal = 'Tanggal harus diisi';
                  return Object.keys(this.errors).length === 0;
              },
              previewBukti(event) {
                  const file = event.target.files[0];
                  if (file) {
                      const reader = new FileReader();
                      reader.onload = (e) => this.previewUrl = e.target.result;
                      reader.readAsDataURL(file);
                  }
              }
          }">
        @csrf
        @method('PATCH')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                <select x-model="form.kategori" name="kategori" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" :class="errors.kategori ? 'border-red-500' : 'border-gray-300'" required>
                    <option value="">Pilih Kategori</option>
                    <option value="Operasional">Operasional</option>
                    <option value="Bahan Baku">Bahan Baku</option>
                    <option value="Utilitas">Utilitas</option>
                    <option value="Gaji">Gaji</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
                <template x-if="errors.kategori"><p class="text-red-500 text-xs mt-1" x-text="errors.kategori"></p></template>
                @error('kategori') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" x-model="form.tanggal" name="tanggal" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" :class="errors.tanggal ? 'border-red-500' : 'border-gray-300'" required>
                <template x-if="errors.tanggal"><p class="text-red-500 text-xs mt-1" x-text="errors.tanggal"></p></template>
                @error('tanggal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
                <input type="text" x-model="form.judul" name="judul" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" :class="errors.judul ? 'border-red-500' : 'border-gray-300'" required>
                <template x-if="errors.judul"><p class="text-red-500 text-xs mt-1" x-text="errors.judul"></p></template>
                @error('judul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea x-model="form.deskripsi" name="deskripsi" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">{{ old('deskripsi', $pengeluaran->deskripsi) }}</textarea>
                @error('deskripsi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah <span class="text-red-500">*</span></label>
                <input type="number" x-model="form.jumlah" name="jumlah" step="0.01" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" :class="errors.jumlah ? 'border-red-500' : 'border-gray-300'" required>
                <template x-if="errors.jumlah"><p class="text-red-500 text-xs mt-1" x-text="errors.jumlah"></p></template>
                @error('jumlah') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Bukti</label>
                <input type="file" name="bukti" accept="image/*" @change="previewBukti" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <div class="mt-2 flex gap-2">
                    <template x-if="previewUrl">
                        <img :src="previewUrl" class="w-32 h-32 object-cover rounded-lg border">
                    </template>
                    <template x-if="!previewUrl && existingBukti">
                        <img :src="existingBukti" class="w-32 h-32 object-cover rounded-lg border">
                    </template>
                </div>
                @error('bukti') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('admin.pengeluaran.index') }}" class="px-4 py-2 text-sm text-gray-600 border rounded-lg hover:bg-gray-50">Batal</a>
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
