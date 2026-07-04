@extends('admin.layouts.app')
@section('title', 'Edit Promo')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.promo.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Promo</h2>
            <p class="text-gray-600">Ubah data promo</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm max-w-2xl">
    <form action="{{ route('admin.promo.update', $promo) }}" method="POST" class="p-6 space-y-4" data-unsaved="true" oninput="this.dataset.dirty='true'" onsubmit="this.dataset.dirty='false'"
          x-data="promoForm()"
          @submit.prevent="submitForm()">
        @csrf
        @method('PATCH')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Promo <span class="text-red-500">*</span></label>
                <input type="text" x-model="form.kode" name="kode" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" :class="errors.kode ? 'border-red-500' : 'border-gray-300'" placeholder="CONTOH10" required>
                <template x-if="errors.kode"><p class="text-red-500 text-xs mt-1" x-text="errors.kode"></p></template>
                @error('kode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Promo <span class="text-red-500">*</span></label>
                <input type="text" x-model="form.nama" name="nama" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" :class="errors.nama ? 'border-red-500' : 'border-gray-300'" required>
                <template x-if="errors.nama"><p class="text-red-500 text-xs mt-1" x-text="errors.nama"></p></template>
                @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea x-model="form.deskripsi" name="deskripsi" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">{{ old('deskripsi', $promo->deskripsi) }}</textarea>
                @error('deskripsi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Promo <span class="text-red-500">*</span></label>
                <select x-model="form.tipe" name="tipe" @change="onTipeChange" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" :class="errors.tipe ? 'border-red-500' : 'border-gray-300'" required>
                    <option value="">Pilih Tipe</option>
                    <option value="persen">Persen (%)</option>
                    <option value="nominal">Nominal (Rp)</option>
                    <option value="buy_get">Buy & Get</option>
                    <option value="free_ongkir">Free Ongkir</option>
                </select>
                <template x-if="errors.tipe"><p class="text-red-500 text-xs mt-1" x-text="errors.tipe"></p></template>
                @error('tipe') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div x-show="form.tipe === 'persen' || form.tipe === 'nominal'" x-transition>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nilai <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="number" x-model="form.nilai" name="nilai" step="0.01" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" :class="errors.nilai ? 'border-red-500' : 'border-gray-300'">
                    <span x-text="form.tipe === 'persen' ? '%' : 'Rp'" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></span>
                </div>
                <template x-if="errors.nilai"><p class="text-red-500 text-xs mt-1" x-text="errors.nilai"></p></template>
                @error('nilai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Min Belanja</label>
                <input type="number" x-model="form.min_belanja" name="min_belanja" value="{{ old('min_belanja', $promo->min_belanja) }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                @error('min_belanja') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div x-show="form.tipe === 'persen'" x-transition>
                <label class="block text-sm font-medium text-gray-700 mb-1">Maks Diskon</label>
                <input type="number" x-model="form.maks_diskon" name="maks_diskon" value="{{ old('maks_diskon', $promo->maks_diskon) }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                @error('maks_diskon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kuota</label>
                <input type="number" x-model="form.kuota" name="kuota" value="{{ old('kuota', $promo->kuota) }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                @error('kuota') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Mulai</label>
                <input type="datetime-local" x-model="form.berlaku_mulai" name="berlaku_mulai" value="{{ old('berlaku_mulai', $promo->berlaku_mulai ? \Carbon\Carbon::parse($promo->berlaku_mulai)->format('Y-m-d\TH:i') : '') }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                @error('berlaku_mulai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Sampai</label>
                <input type="datetime-local" x-model="form.berlaku_sampai" name="berlaku_sampai" value="{{ old('berlaku_sampai', $promo->berlaku_sampai ? \Carbon\Carbon::parse($promo->berlaku_sampai)->format('Y-m-d\TH:i') : '') }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                @error('berlaku_sampai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-2 space-y-2">
            <label class="flex items-center gap-2">
                <input type="checkbox" x-model="form.is_active" name="is_active" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm font-medium text-gray-700">Aktif</span>
            </label>
        </div>

        <template x-if="errors.message">
            <div class="flash-msg flash-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span x-text="errors.message"></span>
            </div>
        </template>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('admin.promo.index') }}" class="px-4 py-2 text-sm text-gray-600 border rounded-lg hover:bg-gray-50">Batal</a>
            <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700 flex items-center gap-2">
                <i class="fa-solid fa-save"></i> Update
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('promoForm', () => ({
        form: {
            kode: '{{ old('kode', $promo->kode) }}',
            nama: '{{ old('nama', $promo->nama) }}',
            deskripsi: '{{ old('deskripsi', $promo->deskripsi) }}',
            tipe: '{{ old('tipe', $promo->tipe) }}',
            nilai: '{{ old('nilai', $promo->nilai) }}',
            min_belanja: '{{ old('min_belanja', $promo->min_belanja) }}',
            maks_diskon: '{{ old('maks_diskon', $promo->maks_diskon) }}',
            kuota: '{{ old('kuota', $promo->kuota) }}',
            berlaku_mulai: '{{ old('berlaku_mulai', $promo->berlaku_mulai ? \Carbon\Carbon::parse($promo->berlaku_mulai)->format('Y-m-d\TH:i') : '') }}',
            berlaku_sampai: '{{ old('berlaku_sampai', $promo->berlaku_sampai ? \Carbon\Carbon::parse($promo->berlaku_sampai)->format('Y-m-d\TH:i') : '') }}',
            is_active: {{ $promo->is_active ? 'true' : 'false' }}
        },
        errors: {},

        onTipeChange() {
            if (this.form.tipe === 'free_ongkir' || this.form.tipe === 'buy_get') {
                this.form.nilai = '';
            }
        },

        validate() {
            this.errors = {};
            if (!this.form.kode.trim()) this.errors.kode = 'Kode promo harus diisi';
            if (!this.form.nama.trim()) this.errors.nama = 'Nama promo harus diisi';
            if (!this.form.tipe) this.errors.tipe = 'Tipe promo harus dipilih';
            if ((this.form.tipe === 'persen' || this.form.tipe === 'nominal') && (!this.form.nilai || this.form.nilai <= 0)) {
                this.errors.nilai = 'Nilai harus diisi dengan angka positif';
            }
            if (this.form.tipe === 'persen' && this.form.nilai > 100) {
                this.errors.nilai = 'Nilai persen tidak boleh lebih dari 100';
            }
            return Object.keys(this.errors).length === 0;
        },

        submitForm() {
            if (!this.validate()) return;

            const formData = new FormData();
            Object.entries(this.form).forEach(([key, val]) => {
                if (key === 'is_active') {
                    formData.append(key, val ? '1' : '0');
                } else if (val !== '' && val !== null) {
                    formData.append(key, val);
                }
            });
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PATCH');

            fetch('{{ route('admin.promo.update', $promo) }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: formData
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || 'Promo berhasil diupdate', timer: 1500, showConfirmButton: false });
                    window.location.href = '{{ route('admin.promo.index') }}';
                } else {
                    if (d.errors) this.errors = Object.assign(this.errors, d.errors);
                    if (d.message) this.errors.message = d.message;
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan server' }));
        }
    }));
});
window.addEventListener('beforeunload', function(e) {
    const form = document.querySelector('form[data-unsaved="true"][data-dirty="true"]');
    if (form) { e.preventDefault(); e.returnValue = ''; }
});
</script>
@endpush
