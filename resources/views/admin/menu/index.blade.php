@use('App\Models\Kategori')

@extends('admin.layouts.app')
@section('title', 'Menu Cafe')

@push('styles')
<style>
    .toggle-checkbox:checked + .toggle-label { background-color: #22c55e; }
    .toggle-checkbox:checked + .toggle-label .toggle-dot { transform: translateX(100%); }
    @media (max-width: 768px) {
        .table-responsive-custom { display: block; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .table-responsive-custom table { min-width: 700px; }
    }
</style>
@endpush

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Menu Cafe</h2>
        <p class="text-gray-600">Kelola menu cafe</p>
    </div>
    <a href="{{ route('admin.menu.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Tambah Menu
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm" x-data="{ get page() { return state.page }, set page(v) { state.page = v }, get perPage() { return state.perPage }, set perPage(v) { state.perPage = v }, get lastPage() { return state.lastPage || 1 }, get total() { return state.total || 0 }, prevPage() { if (this.page > 1) { this.page--; loadMenu(document.getElementById('searchMenu').value, document.getElementById('filterKategori').value) } }, nextPage() { if (this.page < this.lastPage) { this.page++; loadMenu(document.getElementById('searchMenu').value, document.getElementById('filterKategori').value) } }, goToPage(p) { this.page = p; loadMenu(document.getElementById('searchMenu').value, document.getElementById('filterKategori').value) } }">
    <div class="p-4 border-b flex flex-wrap items-center gap-3">
        <div class="relative w-full sm:w-64">
            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="searchMenu" placeholder="Cari menu..." class="pl-10 pr-4 py-2 border rounded-lg w-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <select id="filterKategori" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
            <option value="">Semua Kategori</option>
            @foreach(Kategori::where('is_active', true)->get() as $kat)
                <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
            @endforeach
        </select>
        <div class="flex items-center gap-2 text-sm text-gray-500 sm:ml-auto">
            <span>Total: <strong id="totalCount">0</strong> menu</span>
            <select x-model="perPage" @change="page = 1; loadMenu(document.getElementById('searchMenu').value, document.getElementById('filterKategori').value)" class="border rounded px-2 py-1 text-sm ml-2">
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>
    <div class="overflow-x-auto table-responsive-custom" id="menuTableWrapper">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 text-left text-sm font-semibold text-gray-600">
                    <th class="px-6 py-3 w-16">No</th>
                    <th class="px-6 py-3">Foto</th>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Kategori</th>
                    <th class="px-6 py-3">Harga</th>
                    <th class="px-6 py-3">Stok</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-center">Best Seller</th>
                    <th class="px-6 py-3 text-center">New</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="menuTableBody" class="divide-y divide-gray-100 text-sm">
                <tr class="skeleton-loader"><td colspan="10" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:0.5;"></div><div class="s-cell" style="flex:0.5;"></div><div class="s-cell" style="flex:0.3;"></div><div class="s-cell" style="flex:0.3;"></div><div class="s-cell" style="flex:0.3;"></div><div class="s-cell" style="flex:0.3;"></div><div class="s-cell" style="flex:0 0 60px;"></div></div></td></tr>
                <tr class="skeleton-loader"><td colspan="10" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:0.5;"></div><div class="s-cell" style="flex:0.5;"></div><div class="s-cell" style="flex:0.3;"></div><div class="s-cell" style="flex:0.3;"></div><div class="s-cell" style="flex:0.3;"></div><div class="s-cell" style="flex:0.3;"></div><div class="s-cell" style="flex:0 0 60px;"></div></div></td></tr>
                <tr class="skeleton-loader"><td colspan="10" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:0.5;"></div><div class="s-cell" style="flex:0.5;"></div><div class="s-cell" style="flex:0.3;"></div><div class="s-cell" style="flex:0.3;"></div><div class="s-cell" style="flex:0.3;"></div><div class="s-cell" style="flex:0.3;"></div><div class="s-cell" style="flex:0 0 60px;"></div></div></td></tr>
            </tbody>
        </table>
    </div>
    <div id="menuEmpty" class="hidden"><div class="empty-state"><div class="empty-icon"><i class="fa-solid fa-utensils"></i></div><h4>Belum ada menu</h4><p>Tambahkan menu baru untuk mulai mengelola daftar menu cafe.</p></div></div>
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

function loadMenu(search = '', kategori = '') {
    state.loading = true;
    const tbody = document.getElementById('menuTableBody');
    tbody.innerHTML = skeletonRows(10);
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (kategori) params.append('kategori', kategori);
    params.append('page', state.page);
    params.append('per_page', state.perPage);
    params.append('data', 'all');
    fetch(`{{ route('admin.menu.index') }}?${params.toString()}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        state.loading = false;
        state.lastPage = d.last_page; state.total = d.total; state.page = d.current_page;
        const tbody = document.getElementById('menuTableBody');
        if (d.data && d.data.length) {
            tbody.innerHTML = d.data.map((m, i) => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">${((state.page-1)*state.perPage) + i + 1}</td>
                    <td class="px-6 py-4">
                        ${m.foto ? `<img src="{{ asset('storage/') }}/${m.foto}" class="w-12 h-12 object-cover rounded-lg" alt="${m.nama}">` : `<div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400"><i class="fa-solid fa-image"></i></div>`}
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-800">${m.nama}</td>
                    <td class="px-6 py-4">${m.kategori ? m.kategori.nama : '-'}</td>
                    <td class="px-6 py-4 font-medium">Rp ${new Intl.NumberFormat('id-ID').format(m.harga)}</td>
                    <td class="px-6 py-4">${m.stok !== null ? m.stok : '-'}</td>
                    <td class="px-6 py-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only toggle-checkbox" role="switch" ${m.is_tersedia ? 'checked' : ''} aria-checked="${m.is_tersedia}" aria-label="Toggle ketersediaan ${m.nama}" onchange="toggleTersedia(${m.id}, this)">
                            <div class="w-10 h-5 bg-gray-300 rounded-full shadow-inner toggle-label transition-colors duration-200"></div>
                            <div class="toggle-dot absolute left-0.5 top-0.5 bg-white w-4 h-4 rounded-full transition-transform duration-200 shadow"></div>
                            <span class="ml-2 text-xs ${m.is_tersedia ? 'text-green-600' : 'text-red-600'}" id="tersedia-label-${m.id}">${m.is_tersedia ? 'Tersedia' : 'Habis'}</span>
                        </label>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button onclick="toggleBestSeller(${m.id})" class="text-lg ${m.is_best_seller ? 'text-yellow-500' : 'text-gray-300 hover:text-yellow-400'}" title="${m.is_best_seller ? 'Nonaktifkan Best Seller' : 'Aktifkan Best Seller'}">
                            <i class="fa-solid fa-crown" id="best-seller-icon-${m.id}"></i>
                        </button>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button onclick="toggleNew(${m.id})" class="text-lg ${m.is_new ? 'text-green-500' : 'text-gray-300 hover:text-green-400'}" title="${m.is_new ? 'Nonaktifkan Label New' : 'Aktifkan Label New'}">
                            <i class="fa-solid fa-star" id="new-icon-${m.id}"></i>
                        </button>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ url('admin/menu') }}/${m.id}/edit" class="text-blue-600 hover:text-blue-800 mx-1" title="Edit"><i class="fa-solid fa-edit"></i></a>
                        <button onclick="hapusMenu(${m.id})" class="text-red-600 hover:text-red-800 mx-1" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
            `).join('');
            document.getElementById('totalCount').textContent = d.total;
            document.getElementById('menuTableWrapper').classList.remove('hidden');
            document.getElementById('menuEmpty').classList.add('hidden');
        } else {
            document.getElementById('menuEmpty').classList.add('hidden');
            showEmpty(tbody, 10, 'fa-solid fa-utensils', 'Belum ada menu', 'Tambahkan menu baru untuk mulai mengelola daftar menu cafe.');
            document.getElementById('totalCount').textContent = '0';
        }
    });
}

function toggleTersedia(id, el) {
    fetch(`{{ url('admin/menu') }}/${id}/toggle-tersedia`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            const label = document.getElementById('tersedia-label-' + id);
            if (label) {
                label.textContent = el.checked ? 'Tersedia' : 'Habis';
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

function toggleBestSeller(id) {
    fetch(`{{ url('admin/menu') }}/${id}/toggle-best-seller`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            const icon = document.getElementById('best-seller-icon-' + id);
            const isActive = icon.parentElement.classList.contains('text-yellow-500');
            icon.parentElement.className = isActive ? 'text-gray-300 hover:text-yellow-400 text-lg' : 'text-yellow-500 text-lg';
            Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || 'Status berhasil diubah', timer: 1500, showConfirmButton: false });
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Gagal mengubah status' });
        }
    })
    .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan server' }));
}

function toggleNew(id) {
    fetch(`{{ url('admin/menu') }}/${id}/toggle-new`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            const icon = document.getElementById('new-icon-' + id);
            const isActive = icon.parentElement.classList.contains('text-green-500');
            icon.parentElement.className = isActive ? 'text-gray-300 hover:text-green-400 text-lg' : 'text-green-500 text-lg';
            Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || 'Status berhasil diubah', timer: 1500, showConfirmButton: false });
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Gagal mengubah status' });
        }
    })
    .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan server' }));
}

function hapusMenu(id) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus menu ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            const deleteUrl = '{{ route('admin.menu.destroy', '_ID_') }}'.replace('_ID_', id);
            fetch(deleteUrl, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || 'Menu berhasil dihapus', timer: 1500, showConfirmButton: false });
                    loadMenu(document.getElementById('searchMenu').value, document.getElementById('filterKategori').value);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Gagal menghapus menu' });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan server' }));
        }
    });
}

document.getElementById('searchMenu')?.addEventListener('input', function() {
    state.page = 1;
    loadMenu(this.value, document.getElementById('filterKategori').value);
});
document.getElementById('filterKategori')?.addEventListener('change', function() {
    state.page = 1;
    loadMenu(document.getElementById('searchMenu').value, this.value);
});

loadMenu();
</script>
@endpush
