@extends('admin.layouts.app')
@section('title', 'Detail Transaksi')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.transaksi.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detail Transaksi</h2>
            <p class="text-gray-600">Kode: <strong>{{ $transaksi->kode_transaksi ?? '#' . $transaksi->id }}</strong></p>
        </div>
    </div>
    <div class="flex gap-2">
            @if($transaksi->status !== 'void')
                <a href="{{ route('admin.transaksi.cetak', $transaksi) }}" target="_blank" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg text-sm flex items-center gap-1"><i class="fa-solid fa-print"></i> Cetak</a>
                @if($transaksi->status === 'lunas')
                    <button onclick="voidTransaksi({{ $transaksi->id }})" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm flex items-center gap-1"><i class="fa-solid fa-ban"></i> Void</button>
                @endif
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- Order Items --}}
        @if($transaksi->order && $transaksi->order->items && $transaksi->order->items->count())
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Item Pesanan</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="px-4 py-2 text-gray-600">Menu</th>
                            <th class="px-4 py-2 text-gray-600">Harga</th>
                            <th class="px-4 py-2 text-gray-600">Qty</th>
                            <th class="px-4 py-2 text-gray-600 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($transaksi->order->items as $item)
                        <tr>
                            <td class="px-4 py-2">{{ $item->menu->nama ?? ($item->nama_menu ?? '-') }}</td>
                            <td class="px-4 py-2">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td class="px-4 py-2">{{ $item->jumlah }}</td>
                            <td class="px-4 py-2 text-right font-medium">Rp {{ number_format($item->jumlah * $item->harga, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2">
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right font-semibold">Subtotal</td>
                            <td class="px-4 py-2 text-right font-medium">Rp {{ number_format($transaksi->order->total, 0, ',', '.') }}</td>
                        </tr>
                        @if($transaksi->order->pajak > 0)
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right text-gray-600">Pajak</td>
                            <td class="px-4 py-2 text-right">Rp {{ number_format($transaksi->order->pajak, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        @if($transaksi->order->service_charge > 0)
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right text-gray-600">Service Charge</td>
                            <td class="px-4 py-2 text-right">Rp {{ number_format($transaksi->order->service_charge, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr class="bg-gray-50">
                            <td colspan="3" class="px-4 py-2 text-right font-bold text-lg">Total</td>
                            <td class="px-4 py-2 text-right font-bold text-lg text-green-600">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

        {{-- Booking Info --}}
        @if($transaksi->booking)
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Booking Karaoke</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Ruangan</p>
                    <p class="font-medium">{{ $transaksi->booking->ruangan->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Pemesan</p>
                    <p class="font-medium">{{ $transaksi->booking->nama_pemesan }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Tanggal</p>
                    <p class="font-medium">{{ \Carbon\Carbon::parse($transaksi->booking->tanggal)->isoFormat('DD MMM Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Jam</p>
                    <p class="font-medium">{{ $transaksi->booking->jam_mulai }} - {{ $transaksi->booking->jam_selesai }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="space-y-6">
        {{-- Transaction Info --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Transaksi</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Kode</span>
                    <span class="font-mono font-medium">{{ $transaksi->kode_transaksi ?? '#' . $transaksi->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Tanggal</span>
                    <span class="font-medium">{{ $transaksi->created_at ? \Carbon\Carbon::parse($transaksi->created_at)->isoFormat('DD MMM Y, HH:mm') : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Kasir</span>
                    <span class="font-medium">{{ $transaksi->user->name ?? '-' }}</span>
                </div>
                @if($transaksi->order && $transaksi->order->meja)
                <div class="flex justify-between">
                    <span class="text-gray-500">Meja</span>
                    <span class="font-medium">Meja {{ $transaksi->order->meja->nomor_meja }}</span>
                </div>
                @elseif($transaksi->order && $transaksi->order->tipe_pesanan)
                <div class="flex justify-between">
                    <span class="text-gray-500">Tipe</span>
                    <span class="font-medium">{{ $transaksi->order->tipe_pesanan == 'takeaway' ? 'Take Away' : 'Delivery' }}</span>
                </div>
                @endif
                @if($transaksi->order && $transaksi->order->nama_pelanggan)
                <div class="flex justify-between">
                    <span class="text-gray-500">Pelanggan</span>
                    <span class="font-medium">{{ $transaksi->order->nama_pelanggan }} @if($transaksi->order->no_hp)({{ $transaksi->order->no_hp }})@endif</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-500">Status</span>
                    @php
                        $badge = ['lunas' => 'bg-green-100 text-green-700', 'void' => 'bg-red-100 text-red-700'];
                    @endphp
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $badge[$transaksi->status] ?? 'bg-gray-100' }}">{{ ucfirst($transaksi->status) }}</span>
                </div>
            </div>
        </div>

        {{-- Payment Info --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pembayaran</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Metode</span>
                    <span class="font-medium">{{ $transaksi->metode_bayar ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Bayar</span>
                    <span class="font-bold text-lg text-green-600">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span>
                </div>
                @if($transaksi->nominal_bayar > 0)
                <div class="flex justify-between">
                    <span class="text-gray-500">Dibayar</span>
                    <span class="font-medium">Rp {{ number_format($transaksi->nominal_bayar, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Kembalian</span>
                    <span class="font-medium">Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>
        </div>

        @if($transaksi->status === 'void')
        <div class="bg-red-50 rounded-xl shadow-sm p-6 border border-red-200">
            <h3 class="text-sm font-semibold text-red-800 mb-2"><i class="fa-solid fa-ban"></i> Alasan Void</h3>
            <p class="text-sm text-red-700">{{ $transaksi->alasan_void ?? 'Tidak ada keterangan' }}</p>
            @if($transaksi->voided_by)
                <p class="text-xs text-red-500 mt-1">Oleh: {{ $transaksi->voidedBy->name ?? '-' }}</p>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
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
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Gagal void transaksi' });
                }
            });
        }
    });
}
</script>
@endpush
