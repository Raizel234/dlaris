@extends('admin.layouts.app')
@section('title', 'Pengeluaran')

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
        <h2 class="text-2xl font-bold text-gray-800">Pengeluaran</h2>
        <p class="text-gray-600">Kelola pencatatan pengeluaran</p>
    </div>
    <a href="{{ route('admin.pengeluaran.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Tambah Pengeluaran
    </a>
</div>

{{-- Summary Card --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-5 md:col-span-4 lg:col-span-1">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#fef2f2,#fee2e2);">
                <i class="fa-solid fa-money-bill-wave text-red-500 text-lg"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Pengeluaran</p>
                <p class="text-xl font-bold text-gray-800" id="totalPengeluaran">Rp 0</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm">
    {{-- Filters --}}
    <div class="p-4 border-b flex flex-wrap items-center gap-3">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Dari Tanggal</label>
            <input type="date" id="filterDari" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Sampai Tanggal</label>
            <input type="date" id="filterSampai" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Kategori</label>
            <select id="filterKategori" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Kategori</option>
                <option value="Operasional">Operasional</option>
                <option value="Bahan Baku">Bahan Baku</option>
                <option value="Utilitas">Utilitas</option>
                <option value="Gaji">Gaji</option>
                <option value="Marketing">Marketing</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>
        <button onclick="loadPengeluaran()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 flex items-center gap-1">
            <i class="fa-solid fa-filter"></i> Filter
        </button>
        <div class="flex items-center gap-2 text-sm text-gray-500 sm:ml-auto">
            <span>Total: <strong id="totalCount">0</strong> pengeluaran</span>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto table-responsive-custom">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 text-left text-sm font-semibold text-gray-600">
                    <th class="px-6 py-3 w-16">No</th>
                    <th class="px-6 py-3">Tanggal</th>
                    <th class="px-6 py-3">Kategori</th>
                    <th class="px-6 py-3">Judul</th>
                    <th class="px-6 py-3">Jumlah</th>
                    <th class="px-6 py-3">Keterangan</th>
                    <th class="px-6 py-3">Dibuat Oleh</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="pengeluaranTableBody" class="divide-y divide-gray-100 text-sm">
                <tr class="skeleton-loader"><td colspan="8" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
                <tr class="skeleton-loader"><td colspan="8" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
                <tr class="skeleton-loader"><td colspan="8" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="p-4 border-t flex items-center justify-between" id="paginationWrapper">
        <div class="text-sm text-gray-500" id="paginationInfo"></div>
        <div class="flex gap-2" id="paginationButtons"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPage = 1;

function loadPengeluaran(page = 1) {
    currentPage = page;
    const params = new URLSearchParams();
    const dari = document.getElementById('filterDari').value;
    const sampai = document.getElementById('filterSampai').value;
    const kategori = document.getElementById('filterKategori').value;
    if (dari) params.append('dari', dari);
    if (sampai) params.append('sampai', sampai);
    if (kategori) params.append('kategori', kategori);
    params.append('page', page);

    const tbody = document.getElementById('pengeluaranTableBody');
    tbody.innerHTML = skeletonRows(8);

    fetch(`{{ route('admin.pengeluaran.index') }}?${params.toString()}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.data && d.data.length) {
            tbody.innerHTML = d.data.map((p, i) => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">${(currentPage - 1) * d.per_page + i + 1}</td>
                    <td class="px-6 py-4">${p.tanggal ? new Date(p.tanggal).toLocaleDateString('id-ID') : '-'}</td>
                    <td class="px-6 py-4"><span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">${p.kategori}</span></td>
                    <td class="px-6 py-4 font-medium text-gray-800">${p.judul}</td>
                    <td class="px-6 py-4 font-medium">Rp ${new Intl.NumberFormat('id-ID').format(p.jumlah)}</td>
                    <td class="px-6 py-4 text-gray-500 max-w-[200px] truncate">${p.deskripsi || '-'}</td>
                    <td class="px-6 py-4">${p.created_by ? p.created_by.name || '-' : '-'}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ url('admin/pengeluaran') }}/${p.id}/edit" class="text-blue-600 hover:text-blue-800 mx-1" title="Edit"><i class="fa-solid fa-edit"></i></a>
                        <button onclick="hapusPengeluaran(${p.id})" class="text-red-600 hover:text-red-800 mx-1" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
            `).join('');
            document.getElementById('totalCount').textContent = d.total || d.data.length;
            document.getElementById('totalPengeluaran').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(d.total_jumlah || 0);
            renderPagination(d);
        } else {
            showEmpty(tbody, 8, 'fa-solid fa-money-bill-wave', 'Belum ada pengeluaran', 'Belum ada data pengeluaran yang tercatat');
            document.getElementById('totalCount').textContent = '0';
            document.getElementById('totalPengeluaran').textContent = 'Rp 0';
            document.getElementById('paginationInfo').textContent = '';
            document.getElementById('paginationButtons').innerHTML = '';
        }
    });
}

function renderPagination(d) {
    const info = document.getElementById('paginationInfo');
    const buttons = document.getElementById('paginationButtons');
    info.textContent = `Menampilkan halaman ${d.current_page} dari ${d.last_page} (${d.total} data)`;
    let html = '';
    if (d.current_page > 1) {
        html += `<button onclick="loadPengeluaran(1)" class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50"><i class="fa-solid fa-angles-left"></i></button>`;
        html += `<button onclick="loadPengeluaran(${d.current_page - 1})" class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50"><i class="fa-solid fa-chevron-left"></i></button>`;
    }
    html += `<span class="px-3 py-1 text-sm font-medium">${d.current_page}</span>`;
    if (d.current_page < d.last_page) {
        html += `<button onclick="loadPengeluaran(${d.current_page + 1})" class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50"><i class="fa-solid fa-chevron-right"></i></button>`;
        html += `<button onclick="loadPengeluaran(${d.last_page})" class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50"><i class="fa-solid fa-angles-right"></i></button>`;
    }
    buttons.innerHTML = html;
}

function hapusPengeluaran(id) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus pengeluaran ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            const deleteUrl = '{{ route('admin.pengeluaran.destroy', '_ID_') }}'.replace('_ID_', id);
            fetch(deleteUrl, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || 'Pengeluaran berhasil dihapus', timer: 1500, showConfirmButton: false });
                    loadPengeluaran(currentPage);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Gagal menghapus pengeluaran' });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan server' }));
        }
    });
}

loadPengeluaran();
</script>
@endpush
