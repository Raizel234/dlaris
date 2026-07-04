@extends('admin.layouts.app')
@section('title', 'Pengaturan Sistem')

@section('content')
<div class="max-w-4xl">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                <i class="fa-solid fa-sliders text-emerald-600"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Pengaturan Sistem</h2>
                <p class="text-sm text-gray-500">Konfigurasi toko dan sistem</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.pengaturan.update') }}" method="POST" enctype="multipart/form-data"
          x-data="{
              logoPreview: null,
              previewLogo(event) {
                  const file = event.target.files[0];
                  if (file) {
                      const reader = new FileReader();
                      reader.onload = (e) => this.logoPreview = e.target.result;
                      reader.readAsDataURL(file);
                  }
              }
          }">
        @csrf

        {{-- Identitas Toko --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-5">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <i class="fa-solid fa-store text-emerald-500"></i>
                <h3 class="font-semibold text-gray-800">Identitas Toko</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Toko <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_toko" value="{{ old('nama_toko', $settings['nama_toko'] ?? config('app.name')) }}"
                               class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all"
                               required>
                        @error('nama_toko') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat</label>
                        <textarea name="alamat" rows="2"
                                  class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all">{{ old('alamat', $settings['alamat'] ?? '') }}</textarea>
                        @error('alamat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">No. HP</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $settings['no_hp'] ?? '') }}"
                               class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all">
                        @error('no_hp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Logo Toko</label>
                        <input type="file" name="logo" accept="image/*" @change="previewLogo"
                               class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-600 hover:file:bg-emerald-100 transition-all">
                        <div class="mt-3 flex gap-3">
                            <template x-if="logoPreview">
                                <div class="relative">
                                    <img :src="logoPreview" class="w-24 h-24 object-cover rounded-xl border border-gray-200">
                                    <span class="absolute -top-2 -right-2 bg-emerald-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-medium">Baru</span>
                                </div>
                            </template>
                            <template x-if="!logoPreview && '{{ $settings['logo'] ?? '' }}'">
                                <div class="relative">
                                    <img src="{{ asset('storage/' . ($settings['logo'] ?? '')) }}" class="w-24 h-24 object-cover rounded-xl border border-gray-200">
                                    <span class="absolute -top-2 -right-2 bg-gray-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-medium">Saat ini</span>
                                </div>
                            </template>
                        </div>
                        @error('logo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Keuangan --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-5">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <i class="fa-solid fa-coins text-yellow-500"></i>
                <h3 class="font-semibold text-gray-800">Keuangan</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Pajak (%)</label>
                        <div class="relative">
                            <input type="number" name="pajak" value="{{ old('pajak', $settings['pajak'] ?? 0) }}"
                                   step="0.01" min="0" max="100"
                                   class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all pr-8">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">%</span>
                        </div>
                        @error('pajak') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Service Charge (%)</label>
                        <div class="relative">
                            <input type="number" name="service_charge" value="{{ old('service_charge', $settings['service_charge'] ?? 0) }}"
                                   step="0.01" min="0" max="100"
                                   class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all pr-8">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">%</span>
                        </div>
                        @error('service_charge') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Stok Otomatis --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-5">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-orange-500"></i>
                <h3 class="font-semibold text-gray-800">Manajemen Stok</h3>
            </div>
            <div class="p-6">
                <label class="flex items-center justify-between cursor-pointer">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Kurangi Stok Bahan Otomatis</p>
                        <p class="text-xs text-gray-500 mt-0.5">Stok bahan akan otomatis berkurang saat pembayaran berhasil diproses</p>
                    </div>
                    <div class="relative">
                        <input type="hidden" name="auto_stock_deduction" value="0">
                        <input type="checkbox" name="auto_stock_deduction" value="1" role="switch" {{ ($settings['auto_stock_deduction'] ?? false) ? 'checked' : '' }}
                               class="sr-only peer" id="autoStockToggle">
                        <label for="autoStockToggle" class="inline-flex h-6 w-11 items-center rounded-full bg-gray-300 peer-checked:bg-emerald-500 transition-colors cursor-pointer">
                            <span class="inline-block h-4 w-4 translate-x-1 rounded-full bg-white transition-transform peer-checked:translate-x-6"></span>
                        </label>
                    </div>
                </label>
            </div>
        </div>

        {{-- Jam Operasional --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-5">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <i class="fa-solid fa-clock text-blue-500"></i>
                <h3 class="font-semibold text-gray-800">Jam Operasional</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Jam Buka</label>
                        <input type="time" name="jam_buka" value="{{ old('jam_buka', $settings['jam_buka'] ?? '08:00') }}"
                               class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all">
                        @error('jam_buka') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Jam Tutup</label>
                        <input type="time" name="jam_tutup" value="{{ old('jam_tutup', $settings['jam_tutup'] ?? '22:00') }}"
                               class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all">
                        @error('jam_tutup') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Tombol Simpan --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all">Batal</a>
            <button type="submit"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-all flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-save"></i>
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection
