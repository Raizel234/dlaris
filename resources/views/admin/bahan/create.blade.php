@extends('admin.layouts.app')
@section('title', 'Tambah Bahan')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.bahan.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tambah Bahan</h2>
            <p class="text-gray-600">Tambah bahan baku baru</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm max-w-3xl">
    <form action="{{ route('admin.bahan.store') }}" method="POST" class="p-6 space-y-4" data-unsaved="true" oninput="this.dataset.dirty='true'" onsubmit="this.dataset.dirty='false'"
          x-data="{
              form: { nama: '', satuan: '', stok: '', stok_minimum: '', harga_beli: '', supplier: '', keterangan: '' },
              errors: {},
              menuAssignments: [],
              init() {
                  @if(isset($menus))
                      this.menuAssignments = {{ $menus->map(fn($m) => ['id' => $m->id, 'nama' => $m->nama, 'jumlah' => ''])->toJson() }};
                  @endif
              },
              validate() {
                  this.errors = {};
                  if (!this.form.nama.trim()) this.errors.nama = 'Nama bahan harus diisi';
                  if (!this.form.satuan) this.errors.satuan = 'Satuan harus dipilih';
                  if (this.form.stok === '' || parseFloat(this.form.stok) < 0) this.errors.stok = 'Stok harus diisi dengan angka';
                  if (this.form.stok_minimum === '' || parseFloat(this.form.stok_minimum) < 0) this.errors.stok_minimum = 'Stok minimum harus diisi dengan angka';
                  if (!this.form.harga_beli || parseFloat(this.form.harga_beli) <= 0) this.errors.harga_beli = 'Harga beli harus diisi dengan angka positif';
                  return Object.keys(this.errors).length === 0;
              }
          }">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bahan <span class="text-red-500">*</span></label>
                <input type="text" x-model="form.nama" name="nama" value="{{ old('nama') }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" :class="errors.nama ? 'border-red-500' : 'border-gray-300'" required>
                <template x-if="errors.nama"><p class="text-red-500 text-xs mt-1" x-text="errors.nama"></p></template>
                @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                <select x-model="form.satuan" name="satuan" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" :class="errors.satuan ? 'border-red-500' : 'border-gray-300'" required>
                    <option value="">Pilih Satuan</option>
                    <option value="kg">kg</option>
                    <option value="gr">gr</option>
                    <option value="liter">liter</option>
                    <option value="ml">ml</option>
                    <option value="pcs">pcs</option>
                    <option value="ekor">ekor</option>
                    <option value="ikat">ikat</option>
                </select>
                <template x-if="errors.satuan"><p class="text-red-500 text-xs mt-1" x-text="errors.satuan"></p></template>
                @error('satuan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                <input type="text" x-model="form.supplier" name="supplier" value="{{ old('supplier') }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                @error('supplier') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stok <span class="text-red-500">*</span></label>
                <input type="number" x-model="form.stok" name="stok" value="{{ old('stok') }}" step="0.01" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" :class="errors.stok ? 'border-red-500' : 'border-gray-300'" required>
                <template x-if="errors.stok"><p class="text-red-500 text-xs mt-1" x-text="errors.stok"></p></template>
                @error('stok') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stok Minimum <span class="text-red-500">*</span></label>
                <input type="number" x-model="form.stok_minimum" name="stok_minimum" value="{{ old('stok_minimum') }}" step="0.01" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" :class="errors.stok_minimum ? 'border-red-500' : 'border-gray-300'" required>
                <template x-if="errors.stok_minimum"><p class="text-red-500 text-xs mt-1" x-text="errors.stok_minimum"></p></template>
                @error('stok_minimum') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli <span class="text-red-500">*</span></label>
                <input type="number" x-model="form.harga_beli" name="harga_beli" value="{{ old('harga_beli') }}" step="0.01" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" :class="errors.harga_beli ? 'border-red-500' : 'border-gray-300'" required>
                <template x-if="errors.harga_beli"><p class="text-red-500 text-xs mt-1" x-text="errors.harga_beli"></p></template>
                @error('harga_beli') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea x-model="form.keterangan" name="keterangan" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">{{ old('keterangan') }}</textarea>
                @error('keterangan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Menu Assignments --}}
        <div class="border-t pt-4">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">Penggunaan pada Menu</h4>
            <div class="max-h-60 overflow-y-auto border rounded-lg divide-y text-sm">
                <template x-for="(menu, index) in menuAssignments" :key="menu.id">
                    <div class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50">
                        <input type="hidden" :name="'menus['+index+'][id]'" :value="menu.id">
                        <label class="flex items-center gap-2 min-w-0 flex-1 cursor-pointer">
                            <input type="checkbox" x-model="menu.selected" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-gray-700 truncate" x-text="menu.nama"></span>
                        </label>
                        <template x-if="menu.selected">
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <label class="text-xs text-gray-500">Jumlah:</label>
                                <input type="number" :name="'menus['+index+'][jumlah]'" x-model="menu.jumlah" step="0.01" min="0" class="w-20 border rounded px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="0">
                            </div>
                        </template>
                    </div>
                </template>
                <template x-if="menuAssignments.length === 0">
                    <p class="px-4 py-3 text-gray-400 text-sm">Tidak ada menu tersedia</p>
                </template>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('admin.bahan.index') }}" class="px-4 py-2 text-sm text-gray-600 border rounded-lg hover:bg-gray-50">Batal</a>
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
