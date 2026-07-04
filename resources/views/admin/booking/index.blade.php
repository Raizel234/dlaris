@extends('admin.layouts.app')
@section('title', 'Booking Karaoke')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Booking Karaoke</h2>
        <p class="text-gray-600">Kelola pemesanan ruangan karaoke</p>
    </div>
    <a href="{{ route('admin.booking.calendar') }}" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
        <i class="fa-solid fa-calendar-days"></i> Kalender
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm mb-6">
    <div class="p-4 border-b flex flex-wrap items-center gap-3">
        <select id="filterStatus" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Status</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="ongoing">Ongoing</option>
            <option value="selesai">Selesai</option>
            <option value="dibatalkan">Dibatalkan</option>
        </select>
        <input type="date" id="filterTanggal" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <div class="ml-auto flex items-center gap-2 text-sm text-gray-500">
            <span>Total: <strong id="totalCount">0</strong></span>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 text-left text-sm font-semibold text-gray-600">
                    <th class="px-6 py-3 w-16">No</th>
                    <th class="px-6 py-3">Ruangan</th>
                    <th class="px-6 py-3">Pemesan</th>
                    <th class="px-6 py-3">Tanggal</th>
                    <th class="px-6 py-3">Jam</th>
                    <th class="px-6 py-3">Durasi</th>
                    <th class="px-6 py-3">Total</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="bookingTableBody" class="divide-y divide-gray-100 text-sm">
                <tr class="skeleton-loader"><td colspan="9" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
                <tr class="skeleton-loader"><td colspan="9" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
                <tr class="skeleton-loader"><td colspan="9" class="px-4 py-0"><div class="skeleton-row"><div class="s-cell s-cell-icon"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.5;"></div><div class="s-cell" style="flex:1.2;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:1;"></div><div class="s-cell" style="flex:0 0 80px;"></div></div></td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
function loadBooking(status = '', tanggal = '') {
    const params = new URLSearchParams();
    if (status) params.append('status', status);
    if (tanggal) params.append('tanggal', tanggal);
    params.append('data', 'all');
    const tbody = document.getElementById('bookingTableBody');
    tbody.innerHTML = skeletonRows(9);
    fetch(`{{ route('admin.booking.index') }}?${params.toString()}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.data && d.data.length) {
            tbody.innerHTML = d.data.map((b, i) => {
                const statusBadge = {
                    pending: 'bg-yellow-100 text-yellow-700',
                    confirmed: 'bg-green-100 text-green-700',
                    ongoing: 'bg-blue-100 text-blue-700',
                    selesai: 'bg-gray-100 text-gray-700',
                    dibatalkan: 'bg-red-100 text-red-700'
                };
                const statusLabel = {
                    pending: 'Pending',
                    confirmed: 'Confirmed',
                    ongoing: 'Ongoing',
                    selesai: 'Selesai',
                    dibatalkan: 'Dibatalkan'
                };
                const actions = [];
                if (b.status === 'pending') actions.push(`<button onclick="updateBooking(${b.id}, 'confirmed')" class="text-green-600 hover:text-green-800 mx-1" title="Konfirmasi"><i class="fa-solid fa-check"></i></button>`);
                if (b.status === 'confirmed') actions.push(`<button onclick="updateBooking(${b.id}, 'ongoing')" class="text-blue-600 hover:text-blue-800 mx-1" title="Mulai"><i class="fa-solid fa-play"></i></button>`);
                if (b.status === 'ongoing') actions.push(`<button onclick="updateBooking(${b.id}, 'selesai')" class="text-gray-600 hover:text-gray-800 mx-1" title="Selesai"><i class="fa-solid fa-flag-checkered"></i></button>`);
                if (['pending', 'confirmed'].includes(b.status)) actions.push(`<button onclick="updateBooking(${b.id}, 'dibatalkan')" class="text-red-600 hover:text-red-800 mx-1" title="Batalkan"><i class="fa-solid fa-times"></i></button>`);
                actions.push(`<a href="{{ url('admin/booking') }}/${b.id}" class="text-blue-600 hover:text-blue-800 mx-1" title="Detail"><i class="fa-solid fa-eye"></i></a>`);

                return `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">${i + 1}</td>
                        <td class="px-6 py-4 font-medium text-gray-800">${b.ruangan ? b.ruangan.nama : '-'}</td>
                        <td class="px-6 py-4">${b.nama_pemesan}</td>
                        <td class="px-6 py-4">${b.tanggal}</td>
                        <td class="px-6 py-4">${b.jam_mulai} - ${b.jam_selesai}</td>
                        <td class="px-6 py-4">${b.durasi} jam</td>
                        <td class="px-6 py-4 font-medium">Rp ${new Intl.NumberFormat('id-ID').format(b.total_harga || 0)}</td>
                        <td class="px-6 py-4"><span class="px-2 py-1 rounded-full text-xs font-medium ${statusBadge[b.status]}">${statusLabel[b.status] || b.status}</span></td>
                        <td class="px-6 py-4 text-center">${actions.join('')}</td>
                    </tr>
                `;
            }).join('');
            document.getElementById('totalCount').textContent = d.total || d.data.length;
        } else {
            showEmpty(tbody, 9, 'fa-solid fa-calendar-check', 'Belum ada booking', 'Belum ada pemesanan ruangan karaoke');
            document.getElementById('totalCount').textContent = '0';
        }
    });
}

function updateBooking(id, status) {
    const labels = { confirmed: 'Konfirmasi', ongoing: 'Mulai Sesi', selesai: 'Selesaikan', dibatalkan: 'Batalkan' };
    Swal.fire({
        title: `Konfirmasi ${labels[status]}`,
        text: `Apakah Anda yakin ingin ${labels[status].toLowerCase()} booking ini?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            fetch(`{{ url('admin/booking') }}/${id}/status`, {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ status })
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || `Booking ${labels[status].toLowerCase()} berhasil`, timer: 1500, showConfirmButton: false });
                    loadBooking(document.getElementById('filterStatus').value, document.getElementById('filterTanggal').value);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Gagal mengubah status' });
                }
            });
        }
    });
}

document.getElementById('filterStatus')?.addEventListener('change', function() {
    loadBooking(this.value, document.getElementById('filterTanggal').value);
});
document.getElementById('filterTanggal')?.addEventListener('change', function() {
    loadBooking(document.getElementById('filterStatus').value, this.value);
});

loadBooking();
</script>
@endpush
