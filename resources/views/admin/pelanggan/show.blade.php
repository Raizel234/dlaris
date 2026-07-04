@extends('admin.layouts.app')
@section('title', 'Detail Pelanggan')

@push('styles')
<style>
    @media (max-width: 768px) {
        .table-responsive-custom { display: block; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .table-responsive-custom table { min-width: 600px; }
    }
</style>
@endpush

@section('content')
@php $pelanggan = $user; @endphp
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.pelanggan.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detail Pelanggan</h2>
            <p class="text-gray-600">Informasi lengkap pelanggan</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="space-y-6">
        {{-- Profile Card --}}
        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
            <div class="w-20 h-20 rounded-full mx-auto flex items-center justify-center text-2xl font-bold text-white mb-3"
                 style="background:linear-gradient(135deg,#059669,#10b981);">
                {{ strtoupper(substr($pelanggan->nama ?? $pelanggan->name, 0, 1)) }}
            </div>
            <h3 class="text-lg font-bold text-gray-800">{{ $pelanggan->nama ?? $pelanggan->name }}</h3>
            <p class="text-sm text-gray-500">{{ $pelanggan->email }}</p>
            @if($pelanggan->no_hp)
                <p class="text-sm text-gray-500">{{ $pelanggan->no_hp }}</p>
            @endif
            <div class="mt-3">
                @php
                    $tier = $pelanggan->member_tier ?? 'regular';
                    $tierBadge = [
                        'regular' => 'bg-gray-100 text-gray-700',
                        'silver' => 'bg-yellow-100 text-yellow-700',
                        'gold' => 'bg-blue-100 text-blue-700',
                        'platinum' => 'bg-purple-100 text-purple-700'
                    ];
                @endphp
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $tierBadge[$tier] ?? 'bg-gray-100' }}">
                    {{ ucfirst($tier) }}
                </span>
            </div>
        </div>

        {{-- Stats Summary --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h4 class="text-sm font-semibold text-gray-600 mb-3">Ringkasan</h4>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Poin</span>
                    <span class="font-semibold text-gray-800">{{ number_format($pelanggan->poin ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Kunjungan</span>
                    <span class="font-semibold text-gray-800">{{ optional($pelanggan->pelanggan)->total_kunjungan ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Belanja</span>
                    <span class="font-bold text-green-600">Rp {{ number_format($totalBelanja, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Terakhir Kunjungan</span>
                    <span class="font-medium text-gray-800">{{ optional($pelanggan->pelanggan)->terakhir_kunjungan ? \Carbon\Carbon::parse($pelanggan->pelanggan->terakhir_kunjungan)->isoFormat('DD MMM Y') : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Bergabung</span>
                    <span class="font-medium text-gray-800">{{ $pelanggan->created_at ? \Carbon\Carbon::parse($pelanggan->created_at)->isoFormat('DD MMM Y') : '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-6">
        {{-- Riwayat Transaksi --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Transaksi</h3>
            <div class="overflow-x-auto table-responsive-custom">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="px-4 py-2 text-gray-600">Kode</th>
                            <th class="px-4 py-2 text-gray-600">Tanggal</th>
                            <th class="px-4 py-2 text-gray-600">Total</th>
                            <th class="px-4 py-2 text-gray-600">Metode</th>
                            <th class="px-4 py-2 text-gray-600">Status</th>
                            <th class="px-4 py-2 text-gray-600 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($transaksis ?? [] as $t)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono font-medium text-gray-800">{{ $t->kode_transaksi ?? '#' . $t->id }}</td>
                            <td class="px-4 py-3">{{ $t->created_at ? \Carbon\Carbon::parse($t->created_at)->isoFormat('DD MMM Y, HH:mm') : '-' }}</td>
                            <td class="px-4 py-3 font-medium text-green-600">Rp {{ number_format($t->total ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">{{ $t->metode_pembayaran ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $sBadge = ['selesai' => 'bg-green-100 text-green-700', 'pending' => 'bg-yellow-100 text-yellow-700', 'void' => 'bg-red-100 text-red-700'];
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $sBadge[$t->status] ?? 'bg-gray-100' }}">{{ ucfirst($t->status) }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ url('admin/transaksi', $t->id) }}" class="text-blue-600 hover:text-blue-800" title="Detail"><i class="fa-solid fa-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada transaksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Total Spending Summary --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Belanja</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-green-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-gray-500">Total Transaksi</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalTransaksi ?? 0 }}</p>
                </div>
                <div class="bg-blue-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-gray-500">Total Belanja</p>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($totalBelanja ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="bg-purple-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-gray-500">Rata-rata per Transaksi</p>
                    <p class="text-2xl font-bold text-gray-800">
                        Rp {{ number_format(($totalTransaksi ?? 0) > 0 ? ($totalBelanja ?? 0) / $totalTransaksi : 0, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
