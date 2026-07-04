@extends('admin.layouts.app')
@section('title', 'Pelanggan')

@push('styles')
<style>
    @media (max-width: 768px) {
        .table-responsive-custom { display: block; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .table-responsive-custom table { min-width: 700px; }
    }
</style>
@endpush

@section('content')
<div class="mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Pelanggan</h2>
        <p class="text-gray-600">Daftar pelanggan terdaftar</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm" x-data="pelangganTable()" x-init="init()">
    <div class="p-4 border-b flex flex-wrap items-center justify-between gap-3">
        <div class="relative w-full sm:w-64">
            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" x-model="search" @input.debounce.300ms="fetchData()" placeholder="Cari nama/email..." class="pl-10 pr-4 py-2 border rounded-lg w-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex items-center gap-3 text-sm text-gray-500">
            <select x-model="perPage" @change="fetchData()" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <span>Total: <strong id="totalCount" x-text="total"></strong> pelanggan</span>
        </div>
    </div>
    <div class="overflow-x-auto table-responsive-custom" x-show="loading || items.length > 0">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 text-left text-sm font-semibold text-gray-600">
                    <th class="px-6 py-3 w-16">No</th>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">No HP</th>
                    <th class="px-6 py-3">Member Tier</th>
                    <th class="px-6 py-3">Poin</th>
                    <th class="px-6 py-3">Total Kunjungan</th>
                    <th class="px-6 py-3">Total Belanja</th>
                    <th class="px-6 py-3">Terakhir Kunjungan</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="pelangganTableBody" class="divide-y divide-gray-100 text-sm">
                <template x-if="loading">
                    <tr class="skeleton-loader"><td colspan="10" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
                    <tr class="skeleton-loader"><td colspan="10" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
                    <tr class="skeleton-loader"><td colspan="10" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
                </template>
                <template x-if="!loading && items.length === 0">
                    <tr>
                        <td colspan="10" class="px-6 py-8 text-center text-gray-500 empty-state">
                            <div class="empty-icon"><i class="fa-solid fa-users"></i></div>
                            <h3 class="text-lg font-semibold text-gray-800">Belum ada pelanggan</h3>
                            <p class="text-gray-500">Belum ada pelanggan yang terdaftar</p>
                        </td>
                    </tr>
                </template>
                <template x-for="(p, i) in items" :key="p.id">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4" x-text="(currentPage - 1) * perPage + i + 1"></td>
                        <td class="px-6 py-4 font-medium text-gray-800" x-text="p.nama || p.name"></td>
                        <td class="px-6 py-4" x-text="p.email"></td>
                        <td class="px-6 py-4" x-text="p.no_hp || '-'"></td>
                        <td class="px-6 py-4">
                            <span x-text="p.member_tier || 'Regular'" class="px-2 py-1 rounded-full text-xs font-medium"
                                  :class="{'bg-gray-100 text-gray-700': !p.member_tier || p.member_tier === 'regular', 'bg-yellow-100 text-yellow-700': p.member_tier === 'silver', 'bg-blue-100 text-blue-700': p.member_tier === 'gold', 'bg-purple-100 text-purple-700': p.member_tier === 'platinum'}"></span>
                        </td>
                        <td class="px-6 py-4" x-text="p.poin ?? 0"></td>
                        <td class="px-6 py-4" x-text="p.total_kunjungan ?? 0"></td>
                        <td class="px-6 py-4 font-medium" x-text="p.total_belanja ? 'Rp ' + new Intl.NumberFormat('id-ID').format(p.total_belanja) : 'Rp 0'"></td>
                        <td class="px-6 py-4" x-text="p.terakhir_kunjungan ? new Date(p.terakhir_kunjungan).toLocaleDateString('id-ID') : '-'"></td>
                        <td class="px-6 py-4 text-center">
                            <a :href="'{{ url('admin/pelanggan') }}/' + p.id" class="text-blue-600 hover:text-blue-800 mx-1" title="Detail"><i class="fa-solid fa-eye"></i></a>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    <div id="pelangganEmpty" class="hidden empty-state">
        <div class="empty-icon"><i class="fa-solid fa-users"></i></div>
        <h3 class="text-lg font-semibold text-gray-800">Belum ada pelanggan</h3>
        <p class="text-gray-500">Belum ada pelanggan yang terdaftar</p>
    </div>
    <div class="p-4 border-t flex flex-wrap items-center justify-between gap-3 text-sm text-gray-600" x-show="items.length > 0">
        <span>
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
    Alpine.data('pelangganTable', () => ({
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

            fetch(`{{ route('admin.pelanggan.index') }}?${params.toString()}`, {
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
        }
    }));
});
</script>
@endpush
