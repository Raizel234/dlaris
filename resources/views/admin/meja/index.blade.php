@extends('admin.layouts.app')
@section('title', 'Manajemen Meja')

@push('styles')
<style>
    @media (max-width: 640px) {
        #mejaGrid { grid-template-columns: 1fr !important; }
    }
</style>
@endpush

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-600">
            <i class="fa-solid fa-arrow-left text-lg"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Meja</h2>
            <p class="text-gray-600">Kelola meja cafe</p>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <button @click="$dispatch('open-modal', 'meja-modal')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Meja
        </button>
    </div>
</div>

<div id="mejaContainer" x-data="{ get page() { return state.page }, set page(v) { state.page = v }, get perPage() { return state.perPage }, set perPage(v) { state.perPage = v }, get lastPage() { return state.lastPage || 1 }, get total() { return state.total || 0 }, prevPage() { if (this.page > 1) { this.page--; loadMeja() } }, nextPage() { if (this.page < this.lastPage) { this.page++; loadMeja() } }, goToPage(p) { this.page = p; loadMeja() } }">
    <div class="mb-4 flex items-center justify-between text-sm text-gray-500">
        <span>Total: <strong id="totalCount">0</strong> meja</span>
        <select x-model="perPage" @change="page = 1; loadMeja()" class="border rounded px-2 py-1 text-sm">
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="mejaGrid">
        <div class="skeleton-loader bg-white rounded-xl shadow-sm p-5 border-t-4 border-gray-200"><div class="skeleton-row mb-3"><div class="s-cell" style="flex:1; height:20px;"></div><div class="s-cell" style="width:50px; height:20px;"></div></div><div class="space-y-2"><div class="s-cell" style="height:14px; width:60%;"></div><div class="s-cell" style="height:14px; width:40%;"></div><div class="s-cell" style="height:14px; width:50%;"></div></div></div>
        <div class="skeleton-loader bg-white rounded-xl shadow-sm p-5 border-t-4 border-gray-200"><div class="skeleton-row mb-3"><div class="s-cell" style="flex:1; height:20px;"></div><div class="s-cell" style="width:50px; height:20px;"></div></div><div class="space-y-2"><div class="s-cell" style="height:14px; width:60%;"></div><div class="s-cell" style="height:14px; width:40%;"></div><div class="s-cell" style="height:14px; width:50%;"></div></div></div>
        <div class="skeleton-loader bg-white rounded-xl shadow-sm p-5 border-t-4 border-gray-200"><div class="skeleton-row mb-3"><div class="s-cell" style="flex:1; height:20px;"></div><div class="s-cell" style="width:50px; height:20px;"></div></div><div class="space-y-2"><div class="s-cell" style="height:14px; width:60%;"></div><div class="s-cell" style="height:14px; width:40%;"></div><div class="s-cell" style="height:14px; width:50%;"></div></div></div>
        <div class="skeleton-loader bg-white rounded-xl shadow-sm p-5 border-t-4 border-gray-200"><div class="skeleton-row mb-3"><div class="s-cell" style="flex:1; height:20px;"></div><div class="s-cell" style="width:50px; height:20px;"></div></div><div class="space-y-2"><div class="s-cell" style="height:14px; width:60%;"></div><div class="s-cell" style="height:14px; width:40%;"></div><div class="s-cell" style="height:14px; width:50%;"></div></div></div>
    </div>
    <div id="mejaEmpty" class="hidden empty-state">
        <div class="empty-icon"><i class="fa-solid fa-chair"></i></div>
        <h3 class="text-lg font-semibold text-gray-800">Belum ada meja</h3>
        <p class="text-gray-500">Tambahkan meja baru dengan tombol Tambah Meja</p>
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
<div x-data="mejaForm"
     x-on:open-modal.window="if ($event.detail === 'meja-modal') { open = true; }"
     x-on:close-modal.window="if ($event.detail === 'meja-modal') { open = false; }"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     @keydown.escape.window="open = false">
    <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="open = false"></div>
    <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-xl shadow-xl w-full max-w-md z-10 overflow-hidden">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800" x-text="mode === 'create' ? 'Tambah Meja' : 'Edit Meja'"></h3>
            <button @click="open = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-times"></i></button>
        </div>
        <form @submit.prevent="submitForm" class="p-6 space-y-4">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Meja <span class="text-red-500">*</span></label>
                <input type="text" x-model="form.nomor_meja" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" :class="errors.nomor_meja ? 'border-red-500' : 'border-gray-300'" required>
                <p x-show="errors.nomor_meja" x-text="errors.nomor_meja" class="text-red-500 text-xs mt-1"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas</label>
                <input type="number" x-model="form.kapasitas" min="1" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Area</label>
                <select x-model="form.area" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                    <option value="indoor">Indoor</option>
                    <option value="outdoor">Outdoor</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select x-model="form.status" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                    <option value="tersedia">Tersedia</option>
                    <option value="dipakai">Dipakai</option>
                    <option value="reserved">Reserved</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="open = false" class="px-4 py-2 text-sm text-gray-600 border rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700 flex items-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    <span x-text="mode === 'create' ? 'Simpan' : 'Update'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let state = { page: 1, perPage: 15, lastPage: 1, total: 0, loading: false };

function loadMeja() {
    state.loading = true;
    const grid = document.getElementById('mejaGrid');
    grid.innerHTML = '<div class="skeleton-loader bg-white rounded-xl shadow-sm p-5 border-t-4 border-gray-200"><div class="skeleton-row mb-3"><div class="s-cell" style="flex:1; height:20px;"></div><div class="s-cell" style="width:50px; height:20px;"></div></div><div class="space-y-2"><div class="s-cell" style="height:14px; width:60%;"></div><div class="s-cell" style="height:14px; width:40%;"></div><div class="s-cell" style="height:14px; width:50%;"></div></div></div><div class="skeleton-loader bg-white rounded-xl shadow-sm p-5 border-t-4 border-gray-200"><div class="skeleton-row mb-3"><div class="s-cell" style="flex:1; height:20px;"></div><div class="s-cell" style="width:50px; height:20px;"></div></div><div class="space-y-2"><div class="s-cell" style="height:14px; width:60%;"></div><div class="s-cell" style="height:14px; width:40%;"></div><div class="s-cell" style="height:14px; width:50%;"></div></div></div><div class="skeleton-loader bg-white rounded-xl shadow-sm p-5 border-t-4 border-gray-200"><div class="skeleton-row mb-3"><div class="s-cell" style="flex:1; height:20px;"></div><div class="s-cell" style="width:50px; height:20px;"></div></div><div class="space-y-2"><div class="s-cell" style="height:14px; width:60%;"></div><div class="s-cell" style="height:14px; width:40%;"></div><div class="s-cell" style="height:14px; width:50%;"></div></div></div><div class="skeleton-loader bg-white rounded-xl shadow-sm p-5 border-t-4 border-gray-200"><div class="skeleton-row mb-3"><div class="s-cell" style="flex:1; height:20px;"></div><div class="s-cell" style="width:50px; height:20px;"></div></div><div class="space-y-2"><div class="s-cell" style="height:14px; width:60%;"></div><div class="s-cell" style="height:14px; width:40%;"></div><div class="s-cell" style="height:14px; width:50%;"></div></div></div>';
    const params = new URLSearchParams();
    params.append('page', state.page);
    params.append('per_page', state.perPage);
    params.append('data', 'all');
    fetch(`{{ route('admin.meja.index') }}?${params.toString()}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        state.loading = false;
        state.lastPage = d.last_page; state.total = d.total; state.page = d.current_page;
        const grid = document.getElementById('mejaGrid');
        if (d.data && d.data.length) {
            grid.innerHTML = d.data.map(m => `
                <div class="bg-white rounded-xl shadow-sm p-5 border-t-4 ${m.status === 'tersedia' ? 'border-green-500' : m.status === 'terisi' ? 'border-blue-500' : m.status === 'reserved' ? 'border-yellow-500' : 'border-red-500'}">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-bold text-gray-800">Meja ${m.nomor_meja}</h3>
                        <div class="flex gap-1">
                            <button onclick="editMeja(${m.id})" class="text-blue-600 hover:text-blue-800 text-sm" title="Edit"><i class="fa-solid fa-edit"></i></button>
                            <button onclick="hapusMeja(${m.id})" class="text-red-600 hover:text-red-800 text-sm" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="fa-solid fa-users"></i> <span>Kapasitas: <strong>${m.kapasitas}</strong> orang</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium ${m.area === 'indoor' ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700'}">
                                <i class="fa-solid ${m.area === 'indoor' ? 'fa-building' : 'fa-tree'}"></i> ${m.area === 'indoor' ? 'Indoor' : 'Outdoor'}
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium ${m.status === 'tersedia' ? 'bg-green-100 text-green-700' : m.status === 'terisi' ? 'bg-blue-100 text-blue-700' : m.status === 'reserved' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'}">
                                ${m.status === 'terisi' ? 'Dipakai' : m.status.charAt(0).toUpperCase() + m.status.slice(1)}
                            </span>
                        </div>
                        <div class="pt-2 flex gap-2">
                            <select onchange="updateStatusMeja(${m.id}, this.value)" class="border rounded text-xs px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="tersedia" ${m.status === 'tersedia' ? 'selected' : ''}>Tersedia</option>
                                <option value="dipakai" ${m.status === 'terisi' ? 'selected' : ''}>Dipakai</option>
                                <option value="reserved" ${m.status === 'reserved' ? 'selected' : ''}>Reserved</option>
                                <option value="maintenance" ${m.status === 'maintenance' ? 'selected' : ''}>Maintenance</option>
                            </select>
                            <button onclick="generateQR(${m.id})" class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200" title="Generate QR"><i class="fa-solid fa-qrcode"></i></button>
                        </div>
                    </div>
                </div>
            `).join('');
            document.getElementById('mejaGrid').classList.remove('hidden');
            document.getElementById('mejaEmpty').classList.add('hidden');
        } else {
            document.getElementById('mejaGrid').classList.add('hidden');
            document.getElementById('mejaEmpty').classList.remove('hidden');
        }
    });
}

function updateStatusMeja(id, status) {
    fetch(`{{ url('admin/meja') }}/${id}/status`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
        body: JSON.stringify({ status })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || 'Status berhasil diubah', timer: 1500, showConfirmButton: false });
            loadMeja();
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Gagal mengubah status' });
            loadMeja();
        }
    });
}

function generateQR(id) {
    fetch(`{{ url('admin/meja') }}/${id}/generate-qr`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success && d.qr_url) {
            Swal.fire({
                title: 'QR Code Meja',
                imageUrl: d.qr_url,
                imageWidth: 200,
                imageHeight: 200,
                imageAlt: 'QR Code',
                confirmButtonText: 'Tutup'
            });
        } else {
            Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || 'QR Code berhasil dibuat' });
        }
    });
}

let editMejaId = null;

function editMeja(id) {
    fetch(`{{ url('admin/meja') }}/${id}/edit`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            editMejaId = id;
            const modalEl = document.querySelector('[x-data].fixed');
            if (modalEl) {
                const modalData = Alpine.$data(modalEl);
                modalData.mode = 'edit';
                const statusMap = { 'terisi': 'dipakai' };
                const status = statusMap[d.data.status] || d.data.status;
                modalData.form = { nomor_meja: d.data.nomor_meja, kapasitas: d.data.kapasitas, area: d.data.area, status: status };
                modalData.errors = {};
                modalData.open = true;
            }
        }
    });
}

function hapusMeja(id) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus meja ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            const deleteUrl = '{{ route('admin.meja.destroy', '_ID_') }}'.replace('_ID_', id);
            fetch(deleteUrl, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || 'Meja berhasil dihapus', timer: 1500, showConfirmButton: false });
                    loadMeja();
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Gagal menghapus meja' });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan server' }));
        }
    });
}

document.addEventListener('alpine:init', () => {
    Alpine.data('mejaForm', () => ({
        open: false,
        mode: 'create',
        form: { nomor_meja: '', kapasitas: 2, area: 'indoor', status: 'tersedia' },
        errors: {},
        submitForm() {
            const url = this.mode === 'create' ? `{{ route('admin.meja.store') }}` : `{{ url('admin/meja') }}/${editMejaId}`;
            const method = this.mode === 'create' ? 'POST' : 'PATCH';

            fetch(url, {
                method: method,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(this.form)
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || 'Meja berhasil disimpan', timer: 1500, showConfirmButton: false });
                    this.open = false;
                    this.mode = 'create';
                    this.form = { nomor_meja: '', kapasitas: 2, area: 'indoor', status: 'tersedia' };
                    this.errors = {};
                    editMejaId = null;
                    loadMeja();
                } else {
                    if (d.errors) this.errors = d.errors;
                    if (d.message) Swal.fire({ icon: 'error', title: 'Gagal', text: d.message });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan server' }));
        }
    }));
});

loadMeja();
</script>
@endpush
