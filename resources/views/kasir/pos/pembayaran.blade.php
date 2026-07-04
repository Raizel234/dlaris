@extends('admin.layouts.app')
@section('title', 'Pembayaran')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.pos') }}" class="text-sm text-blue-600 hover:text-blue-800">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke POS
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h2 class="text-lg font-bold text-gray-800">Pembayaran</h2>
        </div>

        <div class="p-6">
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">No. Order</span>
                    <span class="font-bold text-gray-800">{{ $order->nomor_order }}</span>
                </div>
                @if($order->meja)
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">Meja</span>
                    <span class="font-medium text-gray-800">{{ $order->meja->nomor_meja }}</span>
                </div>
                @endif
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">Tanggal</span>
                    <span class="font-medium text-gray-800">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="border-t pt-2 mt-2">
                    <div class="space-y-1">
                        @foreach($order->items as $item)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">{{ $item->jumlah }}x {{ $item->menu->nama }}</span>
                            <span class="font-medium">Rp {{ number_format($item->jumlah * $item->harga, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="border-t pt-2 mt-2 flex items-center justify-between">
                        <span class="font-bold text-gray-800">Total</span>
                        <span class="font-bold text-lg text-amber-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <form id="formPembayaran" class="space-y-4">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                    <select name="metode_bayar" id="metode_bayar" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="tunai">Tunai</option>
                        <option value="transfer">Transfer</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>

                <div id="nominalSection">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Bayar</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                        <input type="number" name="nominal_bayar" id="nominal_bayar" min="0"
                               class="w-full border rounded-lg pl-10 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="0" required>
                    </div>
                    <p id="kembalianText" class="text-sm mt-1 hidden"></p>
                </div>

                <div class="flex gap-3 pt-2">
                    <a href="{{ route('admin.pos') }}" class="flex-1 text-center px-4 py-2 text-sm text-gray-600 border rounded-lg hover:bg-gray-50">Batal</a>
                    <button type="submit" class="flex-1 px-4 py-2 text-sm text-white bg-amber-600 rounded-lg hover:bg-amber-700 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check"></i> Bayar Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const total = {{ $order->total }};

document.getElementById('nominal_bayar')?.addEventListener('input', function() {
    const nominal = parseInt(this.value) || 0;
    const kembalian = nominal - total;
    const el = document.getElementById('kembalianText');

    if (kembalian >= 0) {
        el.className = 'text-sm mt-1 text-emerald-600';
        el.textContent = 'Kembalian: Rp ' + new Intl.NumberFormat('id-ID').format(kembalian);
        el.classList.remove('hidden');
    } else if (nominal > 0) {
        el.className = 'text-sm mt-1 text-red-500';
        el.textContent = 'Kurang: Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(kembalian));
        el.classList.remove('hidden');
    } else {
        el.classList.add('hidden');
    }
});

document.getElementById('metode_bayar')?.addEventListener('change', function() {
    const section = document.getElementById('nominalSection');
    if (this.value === 'tunai') {
        section.classList.remove('hidden');
        document.getElementById('nominal_bayar').required = true;
    } else {
        section.classList.add('hidden');
        document.getElementById('nominal_bayar').required = false;
    }
});

document.getElementById('formPembayaran')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const nominalBayar = document.getElementById('nominal_bayar');
    const metode = document.getElementById('metode_bayar').value;

    if (metode === 'tunai' && (!nominalBayar.value || parseInt(nominalBayar.value) < total)) {
        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Nominal bayar kurang dari total' });
        return;
    }

    const data = {
        metode_bayar: metode,
        nominal_bayar: metode === 'tunai' ? parseInt(nominalBayar.value) : total,
    };

    fetch('{{ route("admin.pos.bayar", $order->id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            Swal.fire({
                icon: 'success',
                title: 'Pembayaran Berhasil!',
                html: `
                    <div class="text-left text-sm space-y-1">
                        <p>Kode: <strong>${d.data.kode_transaksi}</strong></p>
                        <p>Total: <strong>Rp ${new Intl.NumberFormat('id-ID').format(d.data.total)}</strong></p>
                        <p>Metode: <strong class="capitalize">${d.data.metode_bayar}</strong></p>
                    </div>
                `,
                confirmButtonText: '<i class="fa-solid fa-print mr-1"></i> Cetak Struk',
                showCancelButton: true,
                cancelButtonText: 'Selesai',
                confirmButtonColor: '#10b981',
            }).then(r => {
                if (r.isConfirmed) {
                    window.open('{{ url("admin/pos/order") }}/' + d.data.id + '/cetak', '_blank');
                }
                window.location.href = '{{ route("admin.pos") }}';
            });
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: d.message });
        }
    })
    .catch(() => {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan server' });
    });
});
</script>
@endpush
