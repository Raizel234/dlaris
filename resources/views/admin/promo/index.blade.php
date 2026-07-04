@extends('admin.layouts.app')
@section('title', 'Promo')

@push('styles')
<style>
    .toggle-checkbox:checked + .toggle-label { background-color: #22c55e; }
    .toggle-checkbox:checked + .toggle-label .toggle-dot { transform: translateX(100%); }
    @media (max-width: 768px) {
        .table-responsive-custom { display: block; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .table-responsive-custom table { min-width: 800px; }
    }
</style>
@endpush

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Promo & Diskon</h2>
        <p class="text-gray-600">Kelola promo dan diskon yang berlaku</p>
    </div>
    <a href="{{ route('admin.promo.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Tambah Promo
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm" x-data="promoTable()" x-init="init()">
    <div class="p-4 border-b flex flex-wrap items-center justify-between gap-3">
        <div class="relative w-full sm:w-64">
            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" x-model="search" @input.debounce.300ms="fetchData()" placeholder="Cari kode/nama promo..." class="pl-10 pr-4 py-2 border rounded-lg w-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex items-center gap-3 text-sm text-gray-500">
            <select x-model="perPage" @change="fetchData()" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <span>Total: <strong id="totalCount" x-text="total"></strong> promo</span>
        </div>
    </div>
    <div class="overflow-x-auto table-responsive-custom">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 text-left text-sm font-semibold text-gray-600">
                    <th class="px-6 py-3 w-16">No</th>
                    <th class="px-6 py-3">Kode</th>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Tipe</th>
                    <th class="px-6 py-3">Nilai</th>
                    <th class="px-6 py-3">Min Belanja</th>
                    <th class="px-6 py-3">Kuota</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="promoTableBody" class="divide-y divide-gray-100 text-sm">
                <template x-if="loading">
                    <tr class="skeleton-loader"><td colspan="9" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
                    <tr class="skeleton-loader"><td colspan="9" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
                    <tr class="skeleton-loader"><td colspan="9" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
                </template>
                <template x-if="!loading && items.length === 0">
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-gray-500 empty-state">
                            <div class="empty-icon"><i class="fa-solid fa-tags"></i></div>
                            <h3 class="text-lg font-semibold text-gray-800">Belum ada promo</h3>
                            <p class="text-gray-500">Tambahkan promo baru dengan tombol Tambah Promo</p>
                        </td>
                    </tr>
                </template>
                <template x-for="(p, i) in items" :key="p.id">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4" x-text="(currentPage - 1) * perPage + i + 1"></td>
                        <td class="px-6 py-4 font-mono font-medium text-gray-800" x-text="p.kode"></td>
                        <td class="px-6 py-4 font-medium text-gray-800" x-text="p.nama"></td>
                        <td class="px-6 py-4">
                            <span x-text="p.tipe_label || p.tipe" class="px-2 py-1 rounded-full text-xs font-medium"
                                  :class="{'bg-blue-100 text-blue-700': p.tipe === 'persen', 'bg-green-100 text-green-700': p.tipe === 'nominal', 'bg-purple-100 text-purple-700': p.tipe === 'buy_get', 'bg-orange-100 text-orange-700': p.tipe === 'free_ongkir'}"></span>
                        </td>
                        <td class="px-6 py-4" x-text="p.tipe === 'persen' ? p.nilai + '%' : 'Rp ' + new Intl.NumberFormat('id-ID').format(p.nilai)"></td>
                        <td class="px-6 py-4" x-text="p.min_belanja ? 'Rp ' + new Intl.NumberFormat('id-ID').format(p.min_belanja) : '-'"></td>
                        <td class="px-6 py-4" x-text="p.kuota ?? '-'"></td>
                        <td class="px-6 py-4">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only toggle-checkbox" role="switch" :checked="p.is_active" :aria-checked="p.is_active ? 'true' : 'false'" aria-label="Toggle status promo" @change="toggleStatus(p.id, $event.target)">
                                <div class="w-10 h-5 bg-gray-300 rounded-full shadow-inner toggle-label transition-colors duration-200"></div>
                                <div class="toggle-dot absolute left-0.5 top-0.5 bg-white w-4 h-4 rounded-full transition-transform duration-200 shadow"></div>
                                <span class="ml-2 text-xs" :class="p.is_active ? 'text-green-600' : 'text-red-600'" x-text="p.is_active ? 'Aktif' : 'Nonaktif'"></span>
                            </label>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a :href="'{{ url('admin/promo') }}/' + p.id + '/edit'" class="text-blue-600 hover:text-blue-800 mx-1" title="Edit"><i class="fa-solid fa-edit"></i></a>
                            <button @click="hapusPromo(p.id)" class="text-red-600 hover:text-red-800 mx-1" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t flex flex-wrap items-center justify-between gap-3 text-sm text-gray-600">
        <span x-show="items.length > 0">
            Menampilkan <strong x-text="((currentPage - 1) * perPage) + 1"></strong> - <strong x-text="Math.min(currentPage * perPage, total)"></strong> dari <strong x-text="total"></strong> data
        </span>
        <div class="flex items-center gap-1">
            <button @click="goToPage(1)" :disabled="currentPage === 1" class="px-3 py-1 border rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">&laquo;</button>
            <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1" class="px-3 py-1 border rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">&lsaquo;</button>
            <template x-for="page in pages" :key="page">
                <button @click="goToPage(page)" class="px-3 py-1 border rounded-lg" :class="page === currentPage ? 'bg-blue-600 text-white border-blue-600' : 'hover:bg-gray-50'" x-text="page"></button>
            </template>
            <button @click="goToPage(currentPage + 1)" :disabled="currentPage === lastPage" class="px-3 py-1 border rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">&rsaquo;</button>
            <button @click="goToPage(lastPage)" :disabled="currentPage === lastPage" class="px-3 py-1 border rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">&raquo;</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('promoTable', () => ({
        items: [],
        total: 0,
        currentPage: 1,
        lastPage: 1,
        perPage: 10,
        search: '',
        loading: true,

        get pages() {
            const p = [];
            const start = Math.max(1, this.currentPage - 2);
            const end = Math.min(this.lastPage, this.currentPage + 2);
            for (let i = start; i <= end; i++) p.push(i);
            return p;
        },

        init() {
            this.fetchData();
        },

        fetchData() {
            this.loading = true;
            const params = new URLSearchParams({
                page: this.currentPage,
                per_page: this.perPage,
                data: 'all'
            });
            if (this.search) params.append('search', this.search);

            fetch(`{{ route('admin.promo.index') }}?${params.toString()}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(d => {
                if (d.data) {
                    this.items = d.data;
                    this.total = d.total || d.data.length;
                    this.currentPage = d.current_page || 1;
                    this.lastPage = d.last_page || 1;
                } else {
                    this.items = [];
                    this.total = 0;
                }
            })
            .catch(() => { this.items = []; this.total = 0; })
            .finally(() => { this.loading = false; });
        },

        goToPage(page) {
            if (page < 1 || page > this.lastPage) return;
            this.currentPage = page;
            this.fetchData();
        },

        toggleStatus(id, el) {
            const original = el.checked;
            fetch(`{{ url('admin/promo') }}/${id}/toggle`, {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    const item = this.items.find(p => p.id === id);
                    if (item) item.is_active = el.checked;
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || 'Status berhasil diubah', timer: 1500, showConfirmButton: false });
                } else {
                    el.checked = !original;
                    Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Gagal mengubah status' });
                }
            })
            .catch(() => { el.checked = !original; Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan server' }); });
        },

        hapusPromo(id) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus promo ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) {
                    const deleteUrl = '{{ route('admin.promo.destroy', '_ID_') }}'.replace('_ID_', id);
                    fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) {
                            Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || 'Promo berhasil dihapus', timer: 1500, showConfirmButton: false });
                            this.fetchData();
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Gagal menghapus promo' });
                        }
                    })
                    .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan server' }));
                }
            });
        }
    }));
});
</script>
@endpush
