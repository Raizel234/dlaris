@extends('admin.layouts.app')
@section('title', 'Manajemen Karyawan')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Karyawan</h2>
        <p class="text-gray-600">Kelola data karyawan</p>
    </div>
    <a href="{{ route('admin.karyawan.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Tambah Karyawan
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm" x-data="{ get page() { return state.page }, set page(v) { state.page = v }, get perPage() { return state.perPage }, set perPage(v) { state.perPage = v }, get lastPage() { return state.lastPage || 1 }, get total() { return state.total || 0 }, prevPage() { if (state.page > 1) { state.page--; loadKaryawan(document.getElementById('searchKaryawan').value) } }, nextPage() { if (state.page < state.lastPage) { state.page++; loadKaryawan(document.getElementById('searchKaryawan').value) } }, goToPage(p) { state.page = p; loadKaryawan(document.getElementById('searchKaryawan').value) } }">
    <div class="p-4 border-b flex items-center justify-between">
        <div class="relative w-64">
            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="searchKaryawan" placeholder="Cari karyawan..." class="pl-10 pr-4 py-2 border rounded-lg w-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <span>Total: <strong id="totalCount">0</strong> karyawan</span>
            <select x-model="perPage" @change="page = 1; loadKaryawan(document.getElementById('searchKaryawan').value)" class="border rounded px-2 py-1 text-sm ml-2">
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 text-left text-sm font-semibold text-gray-600">
                    <th class="px-6 py-3 w-16">No</th>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Jabatan</th>
                    <th class="px-6 py-3">No. HP</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="karyawanTableBody" class="divide-y divide-gray-100 text-sm">
                <tr class="skeleton-loader"><td colspan="5" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:2;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
                <tr class="skeleton-loader"><td colspan="5" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:2;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
                <tr class="skeleton-loader"><td colspan="5" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:2;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
            </tbody>
        </table>
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
@endsection

@push('scripts')
<script>
let state = { page: 1, perPage: 15, lastPage: 1, total: 0, loading: false };

function loadKaryawan(search = '') {
    state.loading = true;
    const tbody = document.getElementById('karyawanTableBody');
    tbody.innerHTML = skeletonRows(5);
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    params.append('page', state.page);
    params.append('per_page', state.perPage);
    params.append('data', 'all');
    fetch(`{{ route('admin.karyawan.index') }}?${params.toString()}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        state.loading = false;
        state.lastPage = d.last_page;
        state.total = d.total;
        state.page = d.current_page;
        const tbody = document.getElementById('karyawanTableBody');
        if (d.data && d.data.length) {
            tbody.innerHTML = d.data.map((k, i) => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">${((state.page-1)*state.perPage) + i + 1}</td>
                    <td class="px-6 py-4 font-medium text-gray-800">${k.nama}</td>
                    <td class="px-6 py-4">${k.jabatan || '-'}</td>
                    <td class="px-6 py-4">${k.no_hp || '-'}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ url('admin/karyawan') }}/${k.id}/edit" class="text-blue-600 hover:text-blue-800 mx-1" title="Edit"><i class="fa-solid fa-edit"></i></a>
                        <button onclick="hapusKaryawan(${k.id})" class="text-red-600 hover:text-red-800 mx-1" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
            `).join('');
            document.getElementById('totalCount').textContent = d.total;
        } else {
            showEmpty(tbody, 5, 'fa-solid fa-users', 'Belum ada karyawan', 'Tambahkan karyawan baru dengan tombol Tambah Karyawan');
            document.getElementById('totalCount').textContent = '0';
        }
    });
}

function hapusKaryawan(id) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus karyawan ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            const deleteUrl = '{{ route('admin.karyawan.destroy', '_ID_') }}'.replace('_ID_', id);
            fetch(deleteUrl, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || 'Karyawan berhasil dihapus', timer: 1500, showConfirmButton: false });
                    loadKaryawan(document.getElementById('searchKaryawan').value);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Gagal menghapus karyawan' });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan server' }));
        }
    });
}

document.getElementById('searchKaryawan')?.addEventListener('input', function() {
    state.page = 1;
    loadKaryawan(this.value);
});

loadKaryawan();
</script>
@endpush
