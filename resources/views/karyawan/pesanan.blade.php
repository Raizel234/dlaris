@extends('admin.layouts.app')
@section('title', 'Pesanan Aktif')

@push('styles')
<style>
    .order-card {
        border-radius: 16px; border: 1px solid #f1f5f9;
        background: #fff; transition: all 0.3s;
    }
    .order-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.06); }
    .status-menunggu { background: #fef9c3; color: #a16207; }
    .status-diproses { background: #dbeafe; color: #1d4ed8; }
    .status-selesai { background: #dcfce7; color: #15803d; }
    .status-dibatalkan { background: #fef2f2; color: #dc2626; }
    .order-status { font-size: 0.65rem; font-weight: 700; padding: 2px 10px; border-radius: 50px; text-transform: capitalize; }
    .menu-item-img { width: 36px; height: 36px; border-radius: 8px; object-fit: cover; }
    .menu-item-placeholder { width: 36px; height: 36px; border-radius: 8px; background: #f8fafc; display: flex; align-items: center; justify-content: center; color: #cbd5e1; font-size: 0.8rem; }
</style>
@endpush

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">📋 Pesanan Aktif</h2>
        <p class="text-gray-500 text-sm mt-0.5">Pesanan menunggu dan sedang diproses</p>
    </div>
    <span class="text-sm text-gray-400">Total: <strong class="text-gray-700">{{ $orders->count() }}</strong></span>
</div>

@forelse($orders as $order)
    <div class="order-card p-4 mb-3">
        <div class="flex items-start justify-between mb-3">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h4 class="font-bold text-gray-800">#{{ $order->nomor_order ?? $order->id }}</h4>
                    <span class="order-status status-{{ $order->status }}">{{ $order->status }}</span>
                </div>
                <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500">
                    @if($order->meja)
                        <span><i class="fa-solid fa-chair w-4 text-gray-400"></i> Meja {{ $order->meja->nomor_meja }}</span>
                    @endif
                    @if($order->user)
                        <span><i class="fa-solid fa-user w-4 text-gray-400"></i> {{ $order->user->name }}</span>
                    @endif
                    <span><i class="fa-regular fa-clock w-4 text-gray-400"></i> {{ $order->created_at->diffForHumans() }}</span>
                    @if($order->tipe_pesanan)
                        <span><i class="fa-solid fa-{{ $order->tipe_pesanan == 'dine_in' ? 'store' : 'bag-shopping' }} w-4 text-gray-400"></i> {{ $order->tipe_pesanan == 'dine_in' ? 'Dine In' : 'Take Away' }}</span>
                    @endif
                </div>
            </div>
            <div class="text-right flex-shrink-0 ml-4">
                <p class="font-bold text-emerald-600 text-sm">Rp {{ number_format($order->grand_total ?? $order->total, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Items --}}
        @if($order->items->count() > 0)
        <div class="border-t border-gray-50 pt-3">
            <p class="text-xs font-semibold text-gray-400 mb-2 uppercase tracking-wide">Item Pesanan</p>
            @foreach($order->items as $item)
            <div class="flex items-center gap-3 py-1.5">
                @if($item->menu && $item->menu->foto)
                    <img src="{{ asset('storage/' . $item->menu->foto) }}" class="menu-item-img">
                @else
                    <div class="menu-item-placeholder"><i class="fa-solid fa-utensils"></i></div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-700 truncate">{{ $item->menu->nama ?? 'Menu #'.$item->menu_id }}</p>
                </div>
                <span class="text-sm text-gray-500 font-medium">{{ $item->jumlah }}x</span>
                <span class="text-sm text-gray-700 font-medium">Rp {{ number_format($item->jumlah * $item->harga, 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>
        @endif

        @if($order->catatan)
        <div class="border-t border-gray-50 pt-2 mt-2">
            <p class="text-xs text-gray-400"><span class="font-semibold">Catatan:</span> {{ $order->catatan }}</p>
        </div>
        @endif
    </div>
@empty
    <div class="text-center py-16 text-gray-400">
        <i class="fa-solid fa-receipt text-5xl mb-4 block"></i>
        <p class="text-lg font-semibold">Tidak ada pesanan aktif</p>
        <p class="text-sm">Semua pesanan sudah selesai diproses</p>
    </div>
@endforelse
@endsection
