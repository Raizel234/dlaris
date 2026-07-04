@extends('admin.layouts.app')
@section('title', 'Ruangan Karaoke')

@push('styles')
<style>
    .ruangan-card { transition: transform .2s; }
    .ruangan-card:hover { transform: translateY(-3px); }
</style>
@endpush

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Ruangan Karaoke</h2>
        <p class="text-gray-600">Kelola ruangan karaoke</p>
    </div>
    <div class="flex items-center gap-3">
        <button @click="$dispatch('open-modal', 'ruangan-modal')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Ruangan
        </button>
    </div>
</div>

<div id="ruanganContainer" x-data="{ get page() { return state.page }, set page(v) { state.page = v }, get perPage() { return state.perPage }, set perPage(v) { state.perPage = v }, get lastPage() { return state.lastPage || 1 }, get total() { return state.total || 0 }, prevPage() { if (this.page > 1) { this.page--; loadRuangan() } }, nextPage() { if (this.page < this.lastPage) { this.page++; loadRuangan() } }, goToPage(p) { this.page = p; loadRuangan() } }">
    <div class="mb-4 flex items-center justify-between text-sm text-gray-500">
        <span>Total: <strong id="totalCount">0</strong> ruangan</span>
        <select x-model="perPage" @change="page = 1; loadRuangan()" class="border rounded px-2 py-1 text-sm">
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="ruanganGrid">
        <div class="skeleton-loader bg-white rounded-xl shadow-sm overflow-hidden border-t-4 border-gray-200"><div class="w-full h-40 bg-gray-200"></div><div class="p-4 space-y-2"><div class="skeleton-row"><div class="s-cell" style="flex:1; height:20px;"></div><div class="s-cell" style="width:40px; height:20px;"></div></div><div class="s-cell" style="height:14px; width:50%;"></div><div class="s-cell" style="height:14px; width:60%;"></div><div class="s-cell" style="height:14px; width:30%;"></div></div></div>
        <div class="skeleton-loader bg-white rounded-xl shadow-sm overflow-hidden border-t-4 border-gray-200"><div class="w-full h-40 bg-gray-200"></div><div class="p-4 space-y-2"><div class="skeleton-row"><div class="s-cell" style="flex:1; height:20px;"></div><div class="s-cell" style="width:40px; height:20px;"></div></div><div class="s-cell" style="height:14px; width:50%;"></div><div class="s-cell" style="height:14px; width:60%;"></div><div class="s-cell" style="height:14px; width:30%;"></div></div></div>
        <div class="skeleton-loader bg-white rounded-xl shadow-sm overflow-hidden border-t-4 border-gray-200"><div class="w-full h-40 bg-gray-200"></div><div class="p-4 space-y-2"><div class="skeleton-row"><div class="s-cell" style="flex:1; height:20px;"></div><div class="s-cell" style="width:40px; height:20px;"></div></div><div class="s-cell" style="height:14px; width:50%;"></div><div class="s-cell" style="height:14px; width:60%;"></div><div class="s-cell" style="height:14px; width:30%;"></div></div></div>
    </div>
    <div x-show="total > perPage" class="px-4 py-3 flex items-center justify-between text-sm mt-4">
        <span x-text="`Menampilkan ${((page-1)*perPage)+1} - ${Math.min(page*perPage, total)} dari ${total} data`"></span>
        <div class="flex items-center gap-2">
            <button @click="prevPage" :disabled="page <= 1" class="px-3 py-1 rounded border disabled:opacity-50">Prev</button>
            <template x-for="p in lastPage" :key="p">
                <button @click="goToPage(p)" :class="{'bg-blue-600 text-white': page === p, 'border': page !== p}" class="px-3 py-1 rounded" x-show="Math.abs(p - page) < 3 || p === 1 || p === lastPage" x-text="p"></button>
            </template>
            <button @click="nextPage" :disabled="page >= lastPage" class="px-3 py-1 rounded border disabled:opacity-50">Next</button>
        </div>
    </div>
</div>

{{-- Modal Form --}}
<div x-data="ruanganForm"
     x-on:open-modal.window="if ($event.detail === 'ruangan-modal') { open = true; }"
     x-on:close-modal.window="if ($event.detail === 'ruangan-modal') { open = false; }"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     @keydown.escape.window="open = false">
    <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="open = false"></div>
    <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-xl shadow-xl w-full max-w-lg z-10 overflow-hidden">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800" x-text="mode === 'create' ? 'Tambah Ruangan' : 'Edit Ruangan'"></h3>
            <button @click="open = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-times"></i></button>
        </div>
        <form @submit.prevent="submitForm" class="p-6 space-y-4" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ruangan <span class="text-red-500">*</span></label>
                    <input type="text" x-model="form.nama" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" :class="errors.nama ? 'border-red-500' : 'border-gray-300'" required>
                    <p x-show="errors.nama" x-text="errors.nama" class="text-red-500 text-xs mt-1"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas</label>
                    <input type="number" x-model="form.kapasitas" min="1" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tarif / Jam <span class="text-red-500">*</span></label>
                    <input type="number" x-model="form.tarif_per_jam" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" :class="errors.tarif_per_jam ? 'border-red-500' : 'border-gray-300'">
                    <p x-show="errors.tarif_per_jam" x-text="errors.tarif_per_jam" class="text-red-500 text-xs mt-1"></p>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fasilitas</label>
                    <textarea x-model="form.fasilitas" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" placeholder="Pisahkan dengan koma"></textarea>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Ruangan</label>
                    <input type="file" accept="image/*" @change="previewFoto" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <template x-if="previewUrl">
                        <div class="mt-2"><img :src="previewUrl" class="w-32 h-32 object-cover rounded-lg border"></div>
                    </template>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select x-model="form.status" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                        <option value="tersedia">Tersedia</option>
                        <option value="digunakan">Digunakan</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="open = false" class="px-4 py-2 text-sm text-gray-600 border rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700 flex items-center gap-2" x-html="mode === 'create' ? '<i class=\\'fa-solid fa-save\\'></i> Simpan' : '<i class=\\'fa-solid fa-save\\'></i> Update'"></button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let state = { page: 1, perPage: 15, lastPage: 1, total: 0, loading: false };

function loadRuangan() {
    state.loading = true;
    const grid = document.getElementById('ruanganGrid');
    grid.innerHTML = '<div class="skeleton-loader bg-white rounded-xl shadow-sm overflow-hidden border-t-4 border-gray-200"><div class="w-full h-40 bg-gray-200"></div><div class="p-4 space-y-2"><div class="skeleton-row"><div class="s-cell" style="flex:1; height:20px;"></div><div class="s-cell" style="width:40px; height:20px;"></div></div><div class="s-cell" style="height:14px; width:50%;"></div><div class="s-cell" style="height:14px; width:60%;"></div><div class="s-cell" style="height:14px; width:30%;"></div></div></div><div class="skeleton-loader bg-white rounded-xl shadow-sm overflow-hidden border-t-4 border-gray-200"><div class="w-full h-40 bg-gray-200"></div><div class="p-4 space-y-2"><div class="skeleton-row"><div class="s-cell" style="flex:1; height:20px;"></div><div class="s-cell" style="width:40px; height:20px;"></div></div><div class="s-cell" style="height:14px; width:50%;"></div><div class="s-cell" style="height:14px; width:60%;"></div><div class="s-cell" style="height:14px; width:30%;"></div></div></div><div class="skeleton-loader bg-white rounded-xl shadow-sm overflow-hidden border-t-4 border-gray-200"><div class="w-full h-40 bg-gray-200"></div><div class="p-4 space-y-2"><div class="skeleton-row"><div class="s-cell" style="flex:1; height:20px;"></div><div class="s-cell" style="width:40px; height:20px;"></div></div><div class="s-cell" style="height:14px; width:50%;"></div><div class="s-cell" style="height:14px; width:60%;"></div><div class="s-cell" style="height:14px; width:30%;"></div></div></div>';
    const params = new URLSearchParams();
    params.append('page', state.page);
    params.append('per_page', state.perPage);
    params.append('data', 'all');
    fetch(`{{ route('admin.ruangan.index') }}?${params.toString()}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        state.loading = false;
        state.lastPage = d.last_page; state.total = d.total; state.page = d.current_page;
        const grid = document.getElementById('ruanganGrid');
        if (d.data && d.data.length) {
            grid.innerHTML = d.data.map(r => `
                <div class="ruangan-card bg-white rounded-xl shadow-sm overflow-hidden border-t-4 ${r.status === 'tersedia' ? 'border-green-500' : r.status === 'digunakan' ? 'border-blue-500' : 'border-red-500'}">
                    ${r.foto ? `<img src="{{ asset('storage/') }}/${r.foto}" class="w-full h-40 object-cover" alt="${r.nama}">` : `<div class="w-full h-40 bg-gradient-to-br from-purple-500 to-blue-600 flex items-center justify-center"><i class="fa-solid fa-microphone text-white text-5xl"></i></div>`}
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-bold text-gray-800 text-lg">${r.nama}</h3>
                            <div class="flex gap-1">
                                <button onclick="editRuangan(${r.id})" class="text-blue-600 hover:text-blue-800 text-sm" title="Edit"><i class="fa-solid fa-edit"></i></button>
                                <button onclick="hapusRuangan(${r.id})" class="text-red-600 hover:text-red-800 text-sm" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="space-y-1 text-sm text-gray-600">
                            <div><i class="fa-solid fa-users w-5"></i> Kapasitas: <strong>${r.kapasitas}</strong> orang</div>
                            <div><i class="fa-solid fa-clock w-5"></i> Tarif: <strong class="text-green-600">Rp ${new Intl.NumberFormat('id-ID').format(r.tarif_per_jam)}</strong> / jam</div>
                            ${r.fasilitas ? `<div><i class="fa-solid fa-list-check w-5"></i> ${r.fasilitas}</div>` : ''}
                            <div class="pt-2">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium ${r.status === 'tersedia' ? 'bg-green-100 text-green-700' : r.status === 'digunakan' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700'}">
                                    ${r.status.charAt(0).toUpperCase() + r.status.slice(1)}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        } else {
            grid.innerHTML = '<div class="col-span-full empty-state"><div class="empty-icon"><i class="fa-solid fa-microphone"></i></div><h3 class="text-lg font-semibold text-gray-800">Belum ada ruangan</h3><p class="text-gray-500">Tambahkan ruangan karaoke baru dengan tombol Tambah Ruangan</p></div>';
        }
    });
}

let editRuanganId = null;

function editRuangan(id) {
    fetch(`{{ url('admin/ruangan') }}/${id}/edit`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            editRuanganId = id;
            const modalEl = document.querySelector('[x-data].fixed');
            if (modalEl) {
                const modalData = Alpine.$data(modalEl);
                modalData.mode = 'edit';
                modalData.form = {
                    nama: d.data.nama,
                    kapasitas: d.data.kapasitas,
                    tarif_per_jam: d.data.tarif_per_jam,
                    fasilitas: d.data.fasilitas || '',
                    status: d.data.status
                };
                modalData.errors = {};
                modalData.previewUrl = null;
                modalData.open = true;
            }
        }
    });
}

function hapusRuangan(id) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus ruangan ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            const deleteUrl = '{{ route('admin.ruangan.destroy', '_ID_') }}'.replace('_ID_', id);
            fetch(deleteUrl, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || 'Ruangan berhasil dihapus', timer: 1500, showConfirmButton: false });
                    loadRuangan();
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Gagal menghapus ruangan' });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan server' }));
        }
    });
}

document.addEventListener('alpine:init', () => {
    Alpine.data('ruanganForm', () => ({
        open: false,
        mode: 'create',
        form: { nama: '', kapasitas: 2, tarif_per_jam: '', fasilitas: '', status: 'tersedia' },
        errors: {},
        previewUrl: null,
        previewFoto(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => this.previewUrl = e.target.result;
                reader.readAsDataURL(file);
            }
        },
        submitForm() {
            const url = this.mode === 'create' ? `{{ route('admin.ruangan.store') }}` : `{{ url('admin/ruangan') }}/${editRuanganId}`;
            const method = this.mode === 'create' ? 'POST' : 'PATCH';

            const formData = new FormData();
            formData.append('nama', this.form.nama);
            formData.append('kapasitas', this.form.kapasitas);
            formData.append('tarif_per_jam', this.form.tarif_per_jam);
            formData.append('fasilitas', this.form.fasilitas);
            formData.append('status', this.form.status);
            const fotoInput = document.querySelector('[x-data] input[type="file"]');
            if (fotoInput && fotoInput.files[0]) formData.append('foto', fotoInput.files[0]);
            if (this.mode === 'edit') formData.append('_method', 'PATCH');
            formData.append('_token', '{{ csrf_token() }}');

            fetch(url, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || 'Ruangan berhasil disimpan', timer: 1500, showConfirmButton: false });
                    this.open = false;
                    this.mode = 'create';
                    this.form = { nama: '', kapasitas: 2, tarif_per_jam: '', fasilitas: '', status: 'tersedia' };
                    this.errors = {};
                    this.previewUrl = null;
                    editRuanganId = null;
                    loadRuangan();
                } else {
                    if (d.errors) this.errors = d.errors;
                    if (d.message) Swal.fire({ icon: 'error', title: 'Gagal', text: d.message });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan server' }));
        }
    }));
});

loadRuangan();
</script>
@endpush
