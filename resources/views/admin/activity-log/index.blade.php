@extends('admin.layouts.app')
@section('title', 'Riwayat Aktivitas')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Riwayat Aktivitas</h2>
        <p class="text-gray-600">Catatan aktivitas user di sistem</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm" x-data="activityLog()" x-init="init()">
    <div class="p-4 border-b flex flex-wrap items-center gap-3">
        <select x-model="filter.action" @change="fetchData()" class="border rounded-lg px-3 py-2 text-sm w-full sm:w-auto">
            <option value="">Semua Aksi</option>
            <option value="post">Create</option>
            <option value="put">Update</option>
            <option value="patch">Patch</option>
            <option value="delete">Delete</option>
        </select>
        <select x-model="filter.user_id" @change="fetchData()" class="border rounded-lg px-3 py-2 text-sm w-full sm:w-auto">
            <option value="">Semua User</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
        <input type="date" x-model="filter.date" @change="fetchData()" class="border rounded-lg px-3 py-2 text-sm w-full sm:w-auto">
        <div class="sm:ml-auto flex items-center gap-2 text-sm text-gray-500">
            <span>Total: <strong x-text="total"></strong></span>
            <select x-model="perPage" @change="fetchData()" class="border rounded px-2 py-1 text-sm">
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>
    <div class="overflow-x-auto table-responsive-custom">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 text-left text-sm font-semibold text-gray-600">
                    <th class="px-6 py-3">Waktu</th>
                    <th class="px-6 py-3">User</th>
                    <th class="px-6 py-3">Aksi</th>
                    <th class="px-6 py-3">Deskripsi</th>
                    <th class="px-6 py-3">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                <template x-if="loading">
                    <tr class="skeleton-loader"><td colspan="5" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:0.5;"></div><div class="s-cell" style="flex:2;"></div><div class="s-cell" style="flex:1;"></div></div></td></tr>
                </template>
                <template x-if="!loading && items.length === 0">
                    <tr>
                        <td colspan="5"><div class="empty-state"><div class="empty-icon"><i class="fa-solid fa-history"></i></div><h4>Belum ada aktivitas</h4><p>Belum ada catatan aktivitas yang tercatat.</p></div></td>
                    </tr>
                </template>
                <template x-for="(log, i) in items" :key="log.id">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap" x-text="new Date(log.created_at).toLocaleString('id-ID')"></td>
                        <td class="px-6 py-4 font-medium" x-text="log.user?.name || '-'"></td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium"
                                  :class="{'bg-blue-100 text-blue-700': log.action === 'post', 'bg-yellow-100 text-yellow-700': log.action === 'put' || log.action === 'patch', 'bg-red-100 text-red-700': log.action === 'delete'}">
                                <span x-text="log.action.toUpperCase()"></span>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 max-w-xs truncate" x-text="log.description"></td>
                        <td class="px-6 py-4 text-xs text-gray-400 font-mono" x-text="log.ip_address"></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    <div x-show="total > perPage" class="px-4 py-3 border-t flex items-center justify-between text-sm">
        <span x-text="`Menampilkan ${((page-1)*perPage)+1} - ${Math.min(page*perPage, total)} dari ${total} data`"></span>
        <div class="flex items-center gap-2">
            <button @click="prevPage" :disabled="page <= 1" class="px-3 py-1 rounded border disabled:opacity-50">Prev</button>
            <template x-for="p in lastPage" :key="p">
                <button @click="goToPage(p)" :class="{'bg-blue-600 text-white': page === p, 'border': page !== p}" class="px-3 py-1 rounded hidden sm:inline-block" x-show="Math.abs(p - page) < 3 || p === 1 || p === lastPage" x-text="p"></button>
            </template>
            <button @click="nextPage" :disabled="page >= lastPage" class="px-3 py-1 rounded border disabled:opacity-50">Next</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function activityLog() {
    return {
        items: [],
        total: 0,
        page: 1,
        perPage: 25,
        lastPage: 1,
        loading: false,
        filter: { action: '', user_id: '', date: '' },
        init() { this.fetchData(); },
        fetchData() {
            this.loading = true;
            const params = new URLSearchParams({
                page: this.page,
                per_page: this.perPage,
                ...this.filter
            });
            fetch(`{{ route('admin.activity-log.index') }}?${params}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(d => {
                this.items = d.data;
                this.total = d.total;
                this.lastPage = d.last_page;
                this.page = d.current_page;
            })
            .finally(() => this.loading = false);
        },
        prevPage() { if (this.page > 1) { this.page--; this.fetchData(); } },
        nextPage() { if (this.page < this.lastPage) { this.page++; this.fetchData(); } },
        goToPage(p) { this.page = p; this.fetchData(); }
    };
}
</script>
@endpush
