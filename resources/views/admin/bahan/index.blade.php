@extends('admin.layouts.app')
@section('title', 'Manajemen Bahan')

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
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Bahan</h2>
        <p class="text-gray-600">Kelola stok bahan baku</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.bahan.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Bahan
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm">
    {{-- Filters --}}
    <div class="p-4 border-b flex flex-wrap items-center gap-3">
        <div class="relative w-full sm:w-64">
            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="searchBahan" placeholder="Cari bahan..." class="pl-10 pr-4 py-2 border rounded-lg w-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
            <input type="checkbox" id="filterLowStock" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span>Low Stock saja</span>
        </label>
        <div class="flex items-center gap-2 text-sm text-gray-500 sm:ml-auto">
            <span>Total: <strong id="totalCount">0</strong> bahan</span>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto table-responsive-custom">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 text-left text-sm font-semibold text-gray-600">
                    <th class="px-6 py-3 w-16">No</th>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Satuan</th>
                    <th class="px-6 py-3">Stok</th>
                    <th class="px-6 py-3">Stok Minimum</th>
                    <th class="px-6 py-3">Harga Beli</th>
                    <th class="px-6 py-3">Supplier</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Menu Terkait</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="bahanTableBody" class="divide-y divide-gray-100 text-sm">
                <tr class="skeleton-loader"><td colspan="10" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
                <tr class="skeleton-loader"><td colspan="10" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
                <tr class="skeleton-loader"><td colspan="10" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
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

function loadBahan(page = 1) {
    currentPage = page;
    const params = new URLSearchParams();
    const search = document.getElementById('searchBahan').value;
    const lowStock = document.getElementById('filterLowStock').checked;
    if (search) params.append('search', search);
    if (lowStock) params.append('low_stock', '1');
    params.append('page', page);
    const tbody = document.getElementById('bahanTableBody');
    tbody.innerHTML = skeletonRows(10);

    fetch(`{{ route('admin.bahan.index') }}?${params.toString()}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.data && d.data.length) {
            tbody.innerHTML = d.data.map((b, i) => {
                const isLow = parseFloat(b.stok) <= parseFloat(b.stok_minimum);
                return `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">${(currentPage - 1) * d.per_page + i + 1}</td>
                    <td class="px-6 py-4 font-medium text-gray-800">${b.nama}</td>
                    <td class="px-6 py-4">${b.satuan}</td>
                    <td class="px-6 py-4 ${isLow ? 'text-red-600 font-semibold' : ''}">${b.stok}</td>
                    <td class="px-6 py-4">${b.stok_minimum}</td>
                    <td class="px-6 py-4 font-medium">Rp ${new Intl.NumberFormat('id-ID').format(b.harga_beli)}</td>
                    <td class="px-6 py-4">${b.supplier || '-'}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium ${isLow ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'}">
                            ${isLow ? 'Low Stock' : 'Tersedia'}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        ${b.menus && b.menus.length ? b.menus.map(m => `<span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs mr-1 mb-1">${m.nama}</span>`).join('') : '-'}
                    </td>
                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        <button onclick="tambahStok(${b.id}, '${b.nama}', '${b.satuan}')" class="text-green-600 hover:text-green-800 mx-1" title="Tambah Stok"><i class="fa-solid fa-plus-circle"></i></button>
                        <a href="{{ url('admin/bahan') }}/${b.id}/edit" class="text-blue-600 hover:text-blue-800 mx-1" title="Edit"><i class="fa-solid fa-edit"></i></a>
                        <button onclick="hapusBahan(${b.id})" class="text-red-600 hover:text-red-800 mx-1" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>`;
            }).join('');
            document.getElementById('totalCount').textContent = d.total || d.data.length;
            renderPagination(d);
        } else {
            showEmpty(tbody, 10, 'fa-solid fa-boxes', 'Belum ada bahan', 'Tambahkan bahan baku baru dengan tombol Tambah Bahan');
            document.getElementById('totalCount').textContent = '0';
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
        html += `<button onclick="loadBahan(1)" class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50"><i class="fa-solid fa-angles-left"></i></button>`;
        html += `<button onclick="loadBahan(${d.current_page - 1})" class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50"><i class="fa-solid fa-chevron-left"></i></button>`;
    }
    html += `<span class="px-3 py-1 text-sm font-medium">${d.current_page}</span>`;
    if (d.current_page < d.last_page) {
        html += `<button onclick="loadBahan(${d.current_page + 1})" class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50"><i class="fa-solid fa-chevron-right"></i></button>`;
        html += `<button onclick="loadBahan(${d.last_page})" class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50"><i class="fa-solid fa-angles-right"></i></button>`;
    }
    buttons.innerHTML = html;
}

function tambahStok(id, nama, satuan) {
    Swal.fire({
        title: 'Tambah Stok ' + nama,
        html: `<input type="number" id="stokJumlah" class="swal2-input" placeholder="Jumlah (${satuan})" step="0.01" min="0">`,
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#22c55e',
        preConfirm: () => {
            const jumlah = document.getElementById('stokJumlah').value;
            if (!jumlah || parseFloat(jumlah) <= 0) {
                Swal.showValidationMessage('Jumlah harus diisi dengan angka positif');
            }
            return jumlah;
        }
    }).then(result => {
        if (result.isConfirmed) {
            fetch(`{{ url('admin/bahan') }}/${id}/stok-masuk`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ jumlah: result.value })
            })
            .then(r => r.json())
            .then(d => {
                if (d.success || d.message) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || 'Stok berhasil ditambahkan', timer: 1500, showConfirmButton: false });
                    loadBahan(currentPage);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Gagal menambah stok' });
                }
            })
            .catch(() => {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Stok berhasil ditambahkan', timer: 1500, showConfirmButton: false });
                loadBahan(currentPage);
            });
        }
    });
}

function hapusBahan(id) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus bahan ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            const deleteUrl = '{{ route('admin.bahan.destroy', '_ID_') }}'.replace('_ID_', id);
            fetch(deleteUrl, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || 'Bahan berhasil dihapus', timer: 1500, showConfirmButton: false });
                    loadBahan(currentPage);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Gagal menghapus bahan' });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan server' }));
        }
    });
}

document.getElementById('searchBahan')?.addEventListener('input', function() {
    loadBahan();
});
document.getElementById('filterLowStock')?.addEventListener('change', function() {
    loadBahan();
});

loadBahan();
</script>
@endpush
