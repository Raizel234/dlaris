@extends('admin.layouts.app')
@section('title', 'Riwayat Transaksi')

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
    <h2 class="text-2xl font-bold text-gray-800">Riwayat Transaksi</h2>
    <p class="text-gray-600">Daftar transaksi penjualan</p>
</div>

<div class="bg-white rounded-xl shadow-sm" x-data="{ get page() { return state.page }, set page(v) { state.page = v }, get perPage() { return state.perPage }, set perPage(v) { state.perPage = v }, get lastPage() { return state.lastPage || 1 }, get total() { return state.total || 0 }, prevPage() { if (this.page > 1) { this.page--; loadTransaksi() } }, nextPage() { if (this.page < this.lastPage) { this.page++; loadTransaksi() } }, goToPage(p) { this.page = p; loadTransaksi() } }">
    <div class="p-4 border-b flex flex-wrap items-center gap-3">
        <input type="date" id="filterTanggal" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
        <select id="filterMetode" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
            <option value="">Semua Metode</option>
            <option value="tunai">Tunai</option>
            <option value="qris">QRIS</option>
            <option value="kartu">Kartu</option>
            <option value="transfer">Transfer</option>
        </select>
        <select id="filterStatus" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
            <option value="">Semua Status</option>
            <option value="lunas">Lunas</option>
            <option value="void">Void</option>
        </select>
        <div class="sm:ml-auto flex items-center gap-2 text-sm text-gray-500">
            <span>Total: <strong id="totalCount">0</strong></span>
            <select x-model="perPage" @change="page = 1; loadTransaksi()" class="border rounded px-2 py-1 text-sm">
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>
    <div class="overflow-x-auto table-responsive-custom" id="transaksiTableWrapper">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 text-left text-sm font-semibold text-gray-600">
                    <th class="px-6 py-3 w-16">No</th>
                    <th class="px-6 py-3">Kode</th>
                    <th class="px-6 py-3">Tanggal</th>
                    <th class="px-6 py-3">Pelanggan / Meja</th>
                    <th class="px-6 py-3">Total</th>
                    <th class="px-6 py-3">Metode</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="transaksiTableBody" class="divide-y divide-gray-100 text-sm">
                <tr class="skeleton-loader"><td colspan="8" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
                <tr class="skeleton-loader"><td colspan="8" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
                <tr class="skeleton-loader"><td colspan="8" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
            </tbody>
        </table>
    </div>
    <div id="transaksiEmpty" class="hidden empty-state">
        <div class="empty-icon"><i class="fa-solid fa-receipt"></i></div>
        <h3 class="text-lg font-semibold text-gray-800">Belum ada transaksi</h3>
        <p class="text-gray-500">Belum ada riwayat transaksi yang tercatat</p>
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

function loadTransaksi() {
    state.loading = true;
    const tbody = document.getElementById('transaksiTableBody');
    tbody.innerHTML = skeletonRows(8);
    const params = new URLSearchParams({
        tanggal: document.getElementById('filterTanggal').value,
        metode: document.getElementById('filterMetode').value,
        status: document.getElementById('filterStatus').value,
        page: state.page,
        per_page: state.perPage,
        data: 'all'
    });
    fetch(`{{ route('admin.transaksi.index') }}?${params.toString()}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        state.loading = false;
        state.lastPage = d.last_page; state.total = d.total; state.page = d.current_page;
        const tbody = document.getElementById('transaksiTableBody');
            if (d.data && d.data.length) {
            tbody.innerHTML = d.data.map((t, i) => {
                const statusBadge = {
                    lunas: 'bg-green-100 text-green-700',
                    void: 'bg-red-100 text-red-700'
                };
                const namaPelanggan = t.user?.name || t.order?.user?.name || '';
                const mejaInfo = t.order?.meja?.nomor_meja ? 'Meja ' + t.order.meja.nomor_meja : '';
                const takeawayInfo = t.order?.nama_pelanggan ? (t.order.nama_pelanggan + (t.order.no_hp ? ' (' + t.order.no_hp + ')' : '')) : '';
                const displayInfo = namaPelanggan || mejaInfo || takeawayInfo || '-';
                return `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">${((state.page-1)*state.perPage) + i + 1}</td>
                        <td class="px-6 py-4 font-mono font-medium text-gray-800">${t.kode_transaksi || '#' + t.id}</td>
                        <td class="px-6 py-4">${t.created_at ? new Date(t.created_at).toLocaleDateString('id-ID') : '-'}</td>
                        <td class="px-6 py-4">${displayInfo}</td>
                        <td class="px-6 py-4 font-medium text-green-600">Rp ${new Intl.NumberFormat('id-ID').format(t.total || 0)}</td>
                        <td class="px-6 py-4">${t.metode_bayar || '-'}</td>
                        <td class="px-6 py-4"><span class="px-2 py-1 rounded-full text-xs font-medium ${statusBadge[t.status] || 'bg-gray-100'}">${t.status}</span></td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ url('admin/transaksi') }}/${t.id}" class="text-blue-600 hover:text-blue-800 mx-1" title="Detail"><i class="fa-solid fa-eye"></i></a>
                            ${t.status !== 'void' ? `<a href="{{ url('admin/transaksi') }}/${t.id}/cetak" target="_blank" class="text-gray-600 hover:text-gray-800 mx-1" title="Cetak"><i class="fa-solid fa-print"></i></a>` : ''}
                            ${t.status === 'lunas' ? `<button onclick="voidTransaksi(${t.id})" class="text-red-600 hover:text-red-800 mx-1" title="Void"><i class="fa-solid fa-ban"></i></button>` : ''}
                        </td>
                    </tr>
                `;
            }).join('');
            document.getElementById('totalCount').textContent = d.total;
            document.getElementById('transaksiTableWrapper').classList.remove('hidden');
        } else {
            showEmpty(tbody, 8, 'fa-solid fa-receipt', 'Belum ada transaksi', 'Belum ada riwayat transaksi yang tercatat');
            document.getElementById('totalCount').textContent = '0';
        }
    });
}

function voidTransaksi(id) {
    Swal.fire({
        title: 'Void Transaksi',
        html: '<p class="mb-3 text-sm text-gray-600">Apakah Anda yakin ingin void transaksi ini?</p><textarea id="alasanVoid" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Alasan void..." rows="3"></textarea>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Void!',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const alasan = document.getElementById('alasanVoid').value;
            if (!alasan) { Swal.showValidationMessage('Alasan void harus diisi'); return false; }
            return alasan;
        }
    }).then(result => {
        if (result.isConfirmed) {
            fetch(`{{ url('admin/transaksi') }}/${id}/void`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ alasan: result.value })
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || 'Transaksi berhasil di-void', timer: 1500, showConfirmButton: false });
                    loadTransaksi();
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Gagal void transaksi' });
                }
            });
        }
    });
}

document.getElementById('filterTanggal')?.addEventListener('change', function() { state.page = 1; loadTransaksi(); });
document.getElementById('filterMetode')?.addEventListener('change', function() { state.page = 1; loadTransaksi(); });
document.getElementById('filterStatus')?.addEventListener('change', function() { state.page = 1; loadTransaksi(); });

loadTransaksi();
</script>
@endpush
