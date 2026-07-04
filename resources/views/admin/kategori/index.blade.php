@extends('admin.layouts.app')
@section('title', 'Kategori Menu')

@push('styles')
<style>
    .toggle-checkbox:checked + .toggle-label { background-color: #22c55e; }
    .toggle-checkbox:checked + .toggle-label .toggle-dot { transform: translateX(100%); }
    @media (max-width: 768px) {
        .table-responsive-custom { display: block; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .table-responsive-custom table { min-width: 500px; }
    }
</style>
@endpush

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Kategori Menu</h2>
        <p class="text-gray-600">Kelola kategori menu cafe</p>
    </div>
    <button @click="$dispatch('open-modal', 'kategori-modal')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Tambah Kategori
    </button>
</div>

<div class="bg-white rounded-xl shadow-sm" x-data="{ get page() { return state.page }, set page(v) { state.page = v }, get perPage() { return state.perPage }, set perPage(v) { state.perPage = v }, get lastPage() { return state.lastPage || 1 }, get total() { return state.total || 0 }, prevPage() { if (this.page > 1) { this.page--; loadKategori(document.getElementById('searchKategori').value) } }, nextPage() { if (this.page < this.lastPage) { this.page++; loadKategori(document.getElementById('searchKategori').value) } }, goToPage(p) { this.page = p; loadKategori(document.getElementById('searchKategori').value) } }">
    <div class="p-4 border-b flex flex-wrap items-center justify-between gap-3">
        <div class="relative w-full sm:w-64">
            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="searchKategori" placeholder="Cari kategori..." class="pl-10 pr-4 py-2 border rounded-lg w-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <span>Total: <strong id="totalCount">0</strong> kategori</span>
            <select x-model="perPage" @change="page = 1; loadKategori(document.getElementById('searchKategori').value)" class="border rounded px-2 py-1 text-sm ml-2">
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>
    <div class="overflow-x-auto table-responsive-custom" id="kategoriTableWrapper">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 text-left text-sm font-semibold text-gray-600">
                    <th class="px-6 py-3 w-16">No</th>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Ikon</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="kategoriTableBody" class="divide-y divide-gray-100 text-sm">
                <tr class="skeleton-loader"><td colspan="5" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:2;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
                <tr class="skeleton-loader"><td colspan="5" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:2;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
                <tr class="skeleton-loader"><td colspan="5" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:2;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
            </tbody>
        </table>
    </div>
    <div id="kategoriEmpty" class="hidden empty-state">
        <div class="empty-icon"><i class="fa-solid fa-tags"></i></div>
        <h3 class="text-lg font-semibold text-gray-800">Belum ada kategori</h3>
        <p class="text-gray-500">Tambahkan kategori menu baru dengan tombol Tambah Kategori</p>
    </div>
    <div x-show="total > perPage" class="px-4 py-3 border-t flex items-center justify-between text-sm">
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
<div x-data="kategoriForm"
     x-on:open-modal.window="if ($event.detail === 'kategori-modal') { open = true; }"
     x-on:close-modal.window="if ($event.detail === 'kategori-modal') { open = false; }"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     @keydown.escape.window="open = false">
    <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="open = false"></div>
    <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-xl shadow-xl w-full max-w-md z-10 overflow-hidden">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800" x-text="mode === 'create' ? 'Tambah Kategori' : 'Edit Kategori'"></h3>
            <button @click="open = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-times"></i></button>
        </div>
        <form @submit.prevent="submitForm" class="p-6 space-y-4">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                <input type="text" x-model="form.nama" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" :class="errors.nama ? 'border-red-500' : 'border-gray-300'" required>
                <p x-show="errors.nama" x-text="errors.nama" class="text-red-500 text-xs mt-1"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ikon (Font Awesome)</label>
                <input type="text" x-model="form.ikon" placeholder="contoh: fa-solid fa-coffee" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                <p class="text-xs text-gray-400 mt-1">Gunakan class Font Awesome, misal: fa-solid fa-utensils</p>
                <div x-show="form.ikon" class="mt-2 flex items-center gap-2 text-sm text-gray-600">
                    <span>Pratinjau:</span> <i :class="form.ikon" class="text-lg"></i>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Status Aktif</label>
                <input type="checkbox" x-model="form.is_active" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
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

function loadKategori(search = '') {
    state.loading = true;
    const tbody = document.getElementById('kategoriTableBody');
    tbody.innerHTML = skeletonRows(5);
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    params.append('page', state.page);
    params.append('per_page', state.perPage);
    params.append('data', 'all');
    fetch(`{{ route('admin.kategori.index') }}?${params.toString()}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(d => {
            state.loading = false;
            state.lastPage = d.last_page; state.total = d.total; state.page = d.current_page;
            const tbody = document.getElementById('kategoriTableBody');
            if (d.data && d.data.length) {
                tbody.innerHTML = d.data.map((k, i) => `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">${((state.page-1)*state.perPage) + i + 1}</td>
                        <td class="px-6 py-4 font-medium text-gray-800">${k.nama}</td>
                        <td class="px-6 py-4"><i class="${k.ikon || 'fa-solid fa-tag'} text-lg text-blue-600"></i></td>
                        <td class="px-6 py-4">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only toggle-checkbox" role="switch" ${k.is_active ? 'checked' : ''} aria-checked="${k.is_active}" aria-label="Toggle status kategori ${k.nama}" onchange="toggleStatus(${k.id}, this)">
                                <div class="w-10 h-5 bg-gray-300 rounded-full shadow-inner toggle-label transition-colors duration-200"></div>
                                <div class="toggle-dot absolute left-0.5 top-0.5 bg-white w-4 h-4 rounded-full transition-transform duration-200 shadow"></div>
                                <span class="ml-2 text-xs ${k.is_active ? 'text-green-600' : 'text-red-600'}" id="status-label-${k.id}">${k.is_active ? 'Aktif' : 'Nonaktif'}</span>
                            </label>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="editKategori(${k.id})" class="text-blue-600 hover:text-blue-800 mx-1" title="Edit"><i class="fa-solid fa-edit"></i></button>
                            <button onclick="hapusKategori(${k.id})" class="text-red-600 hover:text-red-800 mx-1" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                `).join('');
                document.getElementById('totalCount').textContent = d.total;
                document.getElementById('kategoriTableWrapper').classList.remove('hidden');
            } else {
                showEmpty(tbody, 5, 'fa-solid fa-tags', 'Belum ada kategori', 'Tambahkan kategori menu baru dengan tombol Tambah Kategori');
                document.getElementById('totalCount').textContent = '0';
            }
        });
}

function toggleStatus(id, el) {
    fetch(`{{ url('admin/kategori') }}/${id}/toggle`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            const label = document.getElementById('status-label-' + id);
            if (label) {
                label.textContent = el.checked ? 'Aktif' : 'Nonaktif';
                label.className = 'ml-2 text-xs ' + (el.checked ? 'text-green-600' : 'text-red-600');
            }
            Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || 'Status berhasil diubah', timer: 1500, showConfirmButton: false });
        } else {
            el.checked = !el.checked;
            Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Gagal mengubah status' });
        }
    })
    .catch(() => { el.checked = !el.checked; Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan server' }); });
}

let editId = null;

function editKategori(id) {
    fetch(`{{ url('admin/kategori') }}/${id}/edit`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            editId = id;
            const modalEl = document.querySelector('[x-data].fixed');
            if (modalEl) {
                const modalData = Alpine.$data(modalEl);
                modalData.mode = 'edit';
                modalData.form = { nama: d.data.nama, ikon: d.data.ikon || '', is_active: !!d.data.is_active };
                modalData.errors = {};
                modalData.open = true;
            }
        }
    });
}

function hapusKategori(id) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus kategori ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            const deleteUrl = '{{ route('admin.kategori.destroy', '_ID_') }}'.replace('_ID_', id);
            fetch(deleteUrl, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || 'Kategori berhasil dihapus', timer: 1500, showConfirmButton: false });
                    loadKategori(document.getElementById('searchKategori').value);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Gagal menghapus kategori' });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan server' }));
        }
    });
}

// Modal form component
document.addEventListener('alpine:init', () => {
    Alpine.data('kategoriForm', () => ({
        open: false,
        mode: 'create',
        form: { nama: '', ikon: '', is_active: true },
        errors: {},
        submitForm() {
            const url = this.mode === 'create' ? `{{ route('admin.kategori.store') }}` : `{{ url('admin/kategori') }}/${editId}`;
            const method = this.mode === 'create' ? 'POST' : 'PATCH';
            const payload = { ...this.form, is_active: this.form.is_active ? 1 : 0 };

            fetch(url, {
                method: method,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(payload)
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || 'Kategori berhasil disimpan', timer: 1500, showConfirmButton: false });
                    this.open = false;
                    this.mode = 'create';
                    this.form = { nama: '', ikon: '', is_active: true };
                    this.errors = {};
                    editId = null;
                    loadKategori(document.getElementById('searchKategori').value);
                } else {
                    if (d.errors) this.errors = d.errors;
                    if (d.message) Swal.fire({ icon: 'error', title: 'Gagal', text: d.message });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan server' }));
        }
    }));
});

document.getElementById('searchKategori')?.addEventListener('input', function() {
    state.page = 1;
    loadKategori(this.value);
});

loadKategori();
</script>
@endpush
