@extends('admin.layouts.app')
@section('title', 'Tambah Menu')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.menu.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tambah Menu</h2>
            <p class="text-gray-600">Buat item menu baru</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm max-w-2xl">
    <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4" data-unsaved="true" oninput="this.dataset.dirty='true'" onsubmit="this.dataset.dirty='false'"
          @submit.prevent="if(validate()) $el.submit()"
          x-data="{
              form: { nama: '', kategori_id: '', harga: '', stok: '', deskripsi: '', is_tersedia: true, is_best_seller: false, is_new: false },
              errors: {},
              previewUrl: null,
              validate() {
                  this.errors = {};
                  if (!this.form.nama.trim()) this.errors.nama = 'Nama menu harus diisi';
                  if (!this.form.kategori_id) this.errors.kategori_id = 'Kategori harus dipilih';
                  if (!this.form.harga || this.form.harga <= 0) this.errors.harga = 'Harga harus diisi dengan angka positif';
                  return Object.keys(this.errors).length === 0;
              },
              previewFoto(event) {
                  const file = event.target.files[0];
                  if (file) {
                      const reader = new FileReader();
                      reader.onload = (e) => this.previewUrl = e.target.result;
                      reader.readAsDataURL(file);
                  }
              }
          }">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Menu <span class="text-red-500">*</span></label>
                <input type="text" x-model="form.nama" name="nama" value="{{ old('nama') }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" :class="errors.nama ? 'border-red-500' : 'border-gray-300'" required>
                <template x-if="errors.nama"><p class="text-red-500 text-xs mt-1" x-text="errors.nama"></p></template>
                @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                <select x-model="form.kategori_id" name="kategori_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" :class="errors.kategori_id ? 'border-red-500' : 'border-gray-300'" required>
                    <option value="">Pilih Kategori</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                    @endforeach
                </select>
                <template x-if="errors.kategori_id"><p class="text-red-500 text-xs mt-1" x-text="errors.kategori_id"></p></template>
                @error('kategori_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga <span class="text-red-500">*</span></label>
                <input type="number" x-model="form.harga" name="harga" value="{{ old('harga') }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" :class="errors.harga ? 'border-red-500' : 'border-gray-300'" required>
                <template x-if="errors.harga"><p class="text-red-500 text-xs mt-1" x-text="errors.harga"></p></template>
                @error('harga') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                <input type="number" name="stok" value="{{ old('stok') }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                @error('stok') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">{{ old('deskripsi') }}</textarea>
                @error('deskripsi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Menu</label>
                <input type="file" name="foto" accept="image/*" @change="previewFoto" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <template x-if="previewUrl">
                    <div class="mt-2">
                        <img :src="previewUrl" class="w-32 h-32 object-cover rounded-lg border">
                    </div>
                </template>
                @error('foto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2 space-y-2">
                <label class="flex items-center gap-2">
                    <input type="checkbox" x-model="form.is_tersedia" name="is_tersedia" value="1" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Tersedia</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" x-model="form.is_best_seller" name="is_best_seller" value="1" class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-500">
                    <span class="text-sm font-medium text-gray-700">Best Seller</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" x-model="form.is_new" name="is_new" value="1" class="rounded border-gray-300 text-green-500 focus:ring-green-500">
                    <span class="text-sm font-medium text-gray-700">Menu Baru (New)</span>
                </label>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('admin.menu.index') }}" class="px-4 py-2 text-sm text-gray-600 border rounded-lg hover:bg-gray-50">Batal</a>
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
