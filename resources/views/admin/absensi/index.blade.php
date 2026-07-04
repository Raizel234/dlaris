@use('App\Models\Karyawan')

@extends('admin.layouts.app')
@section('title', 'Absensi')

@push('styles')
<style>
    @media (max-width: 768px) {
        .table-responsive-custom { display: block; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .table-responsive-custom table { min-width: 700px; }
    }
</style>
@endpush

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Absensi Karyawan</h2>
        <p class="text-gray-600">Catat dan pantau kehadiran karyawan</p>
    </div>
    <div x-data="clockButton()" x-init="init()">
        <button @click="toggleClock" class="px-4 py-2 rounded-lg text-sm flex items-center gap-2 font-medium text-white transition-colors"
                :class="clockedIn ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'"
                :disabled="loading">
            <i class="fa-solid" :class="clockedIn ? 'fa-clock' : 'fa-clock'"></i>
            <span x-text="clockedIn ? 'Clock Out' : 'Clock In'"></span>
        </button>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm" x-data="absensiTable()" x-init="init()">
    <div class="p-4 border-b flex flex-wrap items-center gap-3">
        <input type="date" x-model="filter.tanggal" @change="fetchData()" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
        <select x-model="filter.status" @change="fetchData()" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
            <option value="">Semua Status</option>
            <option value="hadir">Hadir</option>
            <option value="izin">Izin</option>
            <option value="sakit">Sakit</option>
            <option value="alpha">Alpha</option>
            <option value="cuti">Cuti</option>
        </select>
        <select x-model="filter.karyawan_id" @change="fetchData()" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
            <option value="">Semua Karyawan</option>
            @foreach(Karyawan::all() as $k)
                <option value="{{ $k->id }}">{{ $k->nama }}</option>
            @endforeach
        </select>
        <div class="sm:ml-auto flex items-center gap-2 text-sm text-gray-500">
            <span>Total: <strong x-text="total"></strong></span>
        </div>
    </div>
    <div class="overflow-x-auto table-responsive-custom" x-show="loading || items.length > 0">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 text-left text-sm font-semibold text-gray-600">
                    <th class="px-6 py-3 w-16">No</th>
                    <th class="px-6 py-3">Karyawan</th>
                    <th class="px-6 py-3">Tanggal</th>
                    <th class="px-6 py-3">Jam Masuk</th>
                    <th class="px-6 py-3">Jam Pulang</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Keterangan</th>
                </tr>
            </thead>
            <tbody id="absensiTableBody" class="divide-y divide-gray-100 text-sm">
                <template x-if="loading">
                    <tr class="skeleton-loader"><td colspan="7" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1.5;"></div></div></td></tr>
                    <tr class="skeleton-loader"><td colspan="7" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1.5;"></div></div></td></tr>
                    <tr class="skeleton-loader"><td colspan="7" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1.5;"></div></div></td></tr>
                </template>
                <template x-if="!loading && items.length === 0">
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500 empty-state">
                            <div class="empty-icon"><i class="fa-solid fa-clock"></i></div>
                            <h3 class="text-lg font-semibold text-gray-800">Belum ada absensi</h3>
                            <p class="text-gray-500">Belum ada data absensi untuk periode ini</p>
                        </td>
                    </tr>
                </template>
                <template x-for="(a, i) in items" :key="a.id">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4" x-text="(currentPage - 1) * perPage + i + 1"></td>
                        <td class="px-6 py-4 font-medium text-gray-800" x-text="a.user ? a.user.name : '-'"></td>
                        <td class="px-6 py-4" x-text="a.tanggal ? new Date(a.tanggal).toLocaleDateString('id-ID') : '-'"></td>
                        <td class="px-6 py-4" x-text="a.jam_masuk || '-'"></td>
                        <td class="px-6 py-4" x-text="a.jam_pulang || '-'"></td>
                        <td class="px-6 py-4">
                            <span x-text="a.status_label || a.status" class="px-2 py-1 rounded-full text-xs font-medium"
                                  :class="{'bg-green-100 text-green-700': a.status === 'hadir', 'bg-yellow-100 text-yellow-700': a.status === 'izin', 'bg-orange-100 text-orange-700': a.status === 'sakit', 'bg-red-100 text-red-700': a.status === 'alpha', 'bg-blue-100 text-blue-700': a.status === 'cuti'}"></span>
                        </td>
                        <td class="px-6 py-4" x-text="a.keterangan || '-'"></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    <div id="absensiEmpty" class="hidden empty-state">
        <div class="empty-icon"><i class="fa-solid fa-clock"></i></div>
        <h3 class="text-lg font-semibold text-gray-800">Belum ada absensi</h3>
        <p class="text-gray-500">Belum ada data absensi untuk periode ini</p>
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
    Alpine.data('absensiTable', () => ({
        items: [],
        total: 0,
        currentPage: 1,
        lastPage: 1,
        perPage: 10,
        loading: true,
        filter: {
            tanggal: new Date().toISOString().split('T')[0],
            status: '',
            karyawan_id: ''
        },

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
            if (this.filter.tanggal) params.append('tanggal', this.filter.tanggal);
            if (this.filter.status) params.append('status', this.filter.status);
            if (this.filter.karyawan_id) params.append('user_id', this.filter.karyawan_id);

            fetch(`{{ route('admin.absensi.index') }}?${params.toString()}`, {
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

    Alpine.data('clockButton', () => ({
        clockedIn: false,
        loading: false,

        init() {
            fetch('{{ route('admin.absensi.index') }}?status=clock_status', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(d => {
                this.clockedIn = d.clocked_in || false;
            })
            .catch(() => {});
        },

        toggleClock() {
            this.loading = true;
            const url = this.clockedIn ? '{{ route('admin.absensi.clock-out') }}' : '{{ route('admin.absensi.clock-in') }}';
            const method = 'POST';

            fetch(url, {
                method: method,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    this.clockedIn = !this.clockedIn;
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || 'Berhasil', timer: 1500, showConfirmButton: false });
                    this.$root.closest('[x-data]') && this.$root.closest('[x-data]').__x && this.$root.closest('[x-data]').__x.$data.fetchData();
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Gagal memproses absensi' });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan server' }))
            .finally(() => { this.loading = false; });
        }
    }));
});
</script>
@endpush
