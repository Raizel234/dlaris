@extends('layouts.pelanggan')
@section('title', 'Riwayat Pesanan')

@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1">Riwayat Pesanan</h2>
            <p class="text-muted mb-0">Daftar pesanan Anda</p>
        </div>
        <a href="{{ route('pelanggan.menu') }}" class="btn btn-success rounded-pill">
            <i class="bi bi-plus-lg me-1"></i>Pesan Baru
        </a>
    </div>

    @if($orders->count() > 0)
        <div class="row g-3">
            @foreach($orders as $order)
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <span class="fw-bold h6 mb-0">{{ $order->nomor_order }}</span>
                                @if($order->meja)
                                <span class="ms-2 text-muted small"><i class="bi bi-table me-1"></i>Meja {{ $order->meja->nomor_meja }}</span>
                                @endif
                            </div>
                            <span class="badge rounded-pill px-3 py-1
                                {{ $order->status === 'selesai' ? 'bg-success-subtle text-success' : '' }}
                                {{ $order->status === 'menunggu' ? 'bg-warning-subtle text-warning' : '' }}
                                {{ $order->status === 'diproses' ? 'bg-info-subtle text-info' : '' }}
                                {{ $order->status === 'dibatalkan' ? 'bg-danger-subtle text-danger' : '' }}">
                                {{ $order->status }}
                            </span>
                        </div>
                        <div class="border-top pt-3">
                            @foreach($order->items as $item)
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">{{ $item->jumlah }}x {{ optional($item->menu)->nama ?? 'Menu telah dihapus' }}</span>
                                <span>Rp {{ number_format($item->jumlah * $item->harga, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                        <hr class="my-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $order->created_at->format('d/m/Y H:i') }}</small>
                            <span class="fw-bold text-success fs-6">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                        @if($order->transaksi)
                        <div class="mt-2 small text-muted">
                            <i class="bi bi-check-circle text-success me-1"></i>Lunas via {{ $order->transaksi->metode_bayar }} ({{ $order->transaksi->kode_transaksi }})
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4 d-flex justify-content-center">
            {{ $orders->links() }}
        </div>
    @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-receipt display-1 mb-3 d-block"></i>
            <h5>Belum ada riwayat pesanan</h5>
            <p class="mb-3">Pesan menu favorit Anda sekarang!</p>
            <a href="{{ route('pelanggan.menu') }}" class="btn btn-success rounded-pill">
                <i class="bi bi-menu-app me-1"></i>Pesan Sekarang
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<style>
.pagination { margin-bottom: 0; }
.page-link { border-radius: 50px !important; margin: 0 2px; color: #059669; }
.page-item.active .page-link { background-color: #059669; border-color: #059669; }
</style>
@endpush
