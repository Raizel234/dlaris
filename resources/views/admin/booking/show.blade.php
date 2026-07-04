@extends('admin.layouts.app')
@section('title', 'Detail Booking')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.booking.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detail Booking</h2>
            <p class="text-gray-600">Informasi lengkap pemesanan ruangan</p>
        </div>
    </div>
    <div class="flex gap-2" id="detailActions">
        @if(in_array($booking->status, ['pending', 'confirmed', 'ongoing']))
            @if($booking->status == 'pending')
                <button onclick="updateBooking({{ $booking->id }}, 'confirmed')" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm flex items-center gap-1"><i class="fa-solid fa-check"></i> Konfirmasi</button>
            @endif
            @if($booking->status == 'confirmed')
                <button onclick="updateBooking({{ $booking->id }}, 'ongoing')" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm flex items-center gap-1"><i class="fa-solid fa-play"></i> Mulai</button>
            @endif
            @if($booking->status == 'ongoing')
                <button onclick="updateBooking({{ $booking->id }}, 'selesai')" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg text-sm flex items-center gap-1"><i class="fa-solid fa-flag-checkered"></i> Selesai</button>
            @endif
            @if(in_array($booking->status, ['pending', 'confirmed']))
                <button onclick="updateBooking({{ $booking->id }}, 'dibatalkan')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm flex items-center gap-1"><i class="fa-solid fa-times"></i> Batalkan</button>
            @endif
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- Booking Info --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Booking</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Nama Pemesan</p>
                    <p class="font-medium text-gray-800">{{ $booking->nama_pemesan }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">No. Telepon</p>
                    <p class="font-medium text-gray-800">{{ $booking->no_telp ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Ruangan</p>
                    <p class="font-medium text-gray-800">{{ $booking->ruangan->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tanggal</p>
                    <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($booking->tanggal)->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Jam</p>
                    <p class="font-medium text-gray-800">{{ $booking->jam_mulai }} - {{ $booking->jam_selesai }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Durasi</p>
                    <p class="font-medium text-gray-800">{{ $booking->durasi }} Jam</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Bayar</p>
                    <p class="font-bold text-lg text-green-600">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    @php
                        $badge = ['pending' => 'bg-yellow-100 text-yellow-700', 'confirmed' => 'bg-green-100 text-green-700', 'ongoing' => 'bg-blue-100 text-blue-700', 'selesai' => 'bg-gray-100 text-gray-700', 'dibatalkan' => 'bg-red-100 text-red-700'];
                    @endphp
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $badge[$booking->status] ?? 'bg-gray-100' }}">{{ ucfirst($booking->status) }}</span>
                </div>
            </div>
            @if($booking->catatan)
            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">Catatan</p>
                <p class="text-gray-800">{{ $booking->catatan }}</p>
            </div>
            @endif
        </div>

        {{-- Menu Orders --}}
        @if($booking->orderItems && $booking->orderItems->count())
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pesanan Menu</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="px-4 py-2 text-gray-600">Menu</th>
                            <th class="px-4 py-2 text-gray-600">Qty</th>
                            <th class="px-4 py-2 text-gray-600">Harga</th>
                            <th class="px-4 py-2 text-gray-600 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($booking->orderItems as $item)
                        <tr>
                            <td class="px-4 py-2">{{ $item->menu->nama ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $item->qty }}</td>
                            <td class="px-4 py-2">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 text-right font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <div class="space-y-6">
        {{-- Status Timeline --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Status</h3>
            @php
                $timeline = collect($booking->statusHistories ?? []);
            @endphp
            @if($timeline->count())
                <div class="space-y-3">
                    @foreach($timeline as $log)
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-3 h-3 rounded-full {{ $loop->first ? 'bg-blue-500' : 'bg-gray-300' }}"></div>
                            @if(!$loop->last)<div class="w-0.5 h-full bg-gray-200"></div>@endif
                        </div>
                        <div class="pb-3">
                            <p class="text-sm font-medium text-gray-800">{{ ucfirst($log->status) }}</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($log->created_at)->isoFormat('DD MMM HH:mm') }}</p>
                            @if($log->keterangan)<p class="text-xs text-gray-600">{{ $log->keterangan }}</p>@endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="space-y-3">
                    @foreach(['dibuat' => $booking->created_at, $booking->status => now()] as $status => $time)
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ ucfirst($status) }}</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($time)->isoFormat('DD MMM HH:mm') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Payment Info --}}
        @if($booking->pembayaran)
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pembayaran</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Metode</span>
                    <span class="font-medium">{{ $booking->pembayaran->metode ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Status</span>
                    <span class="font-medium {{ $booking->pembayaran->status == 'lunas' ? 'text-green-600' : 'text-yellow-600' }}">{{ ucfirst($booking->pembayaran->status ?? '-') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total</span>
                    <span class="font-bold text-green-600">Rp {{ number_format($booking->pembayaran->total ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
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
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message || 'Status berhasil diubah', timer: 1500, showConfirmButton: false });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Gagal mengubah status' });
                }
            });
        }
    });
}
</script>
@endpush
