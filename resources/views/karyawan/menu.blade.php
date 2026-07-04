@extends('admin.layouts.app')
@section('title', 'Daftar Menu')

@push('styles')
<style>
    .menu-card {
        border-radius: 16px; border: 1px solid #f1f5f9;
        overflow: hidden; transition: all 0.3s;
        background: #fff;
    }
    .menu-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.08); }
    .menu-img { width: 100%; height: 160px; object-fit: cover; }
    .menu-img-placeholder {
        width: 100%; height: 160px;
        display: flex; align-items: center; justify-content: center;
        background: #f8fafc; color: #cbd5e1; font-size: 2.5rem;
    }
    .badge-tersedia { background: #dcfce7; color: #15803d; font-size: 0.65rem; font-weight: 700; padding: 2px 8px; border-radius: 50px; }
    .badge-habis { background: #fef2f2; color: #dc2626; font-size: 0.65rem; font-weight: 700; padding: 2px 8px; border-radius: 50px; }
    .kategori-btn {
        padding: 0.5rem 1.2rem; border-radius: 50px; font-size: 0.82rem; font-weight: 600;
        transition: all 0.2s; border: 1.5px solid #e2e8f0; background: #fff; color: #64748b;
        cursor: pointer; white-space: nowrap;
    }
    .kategori-btn:hover { border-color: #059669; color: #059669; }
    .kategori-btn.active { background: #059669; color: #fff; border-color: #059669; }
</style>
@endpush

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">📋 Daftar Menu</h2>
        <p class="text-gray-500 text-sm mt-0.5">Menu yang tersedia hari ini</p>
    </div>
    <div class="text-sm text-gray-400">
        Total <strong class="text-gray-700">{{ $menus->total() }}</strong> menu
    </div>
</div>

{{-- Filter Kategori --}}
<div class="flex gap-2 overflow-x-auto pb-2 mb-5" id="kategoriFilters">
    <button class="kategori-btn active" data-kategori="" onclick="filterMenu('')">Semua</button>
    @foreach($kategoris as $kat)
        <button class="kategori-btn" data-kategori="{{ $kat->id }}" onclick="filterMenu('{{ $kat->id }}')">{{ $kat->nama }}</button>
    @endforeach
</div>

{{-- Search --}}
<div class="relative mb-5 max-w-md">
    <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
    <input type="text" id="searchMenu" placeholder="Cari menu..." value="{{ request('search') }}"
           class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
</div>

{{-- Menu Grid --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="menuGrid">
    @forelse($menus as $menu)
    <div class="menu-card">
        @if($menu->foto)
            <img src="{{ asset('storage/' . $menu->foto) }}" class="menu-img" alt="{{ $menu->nama }}">
        @else
            <div class="menu-img-placeholder"><i class="fa-solid fa-utensils"></i></div>
        @endif
        <div class="p-3.5">
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs text-gray-400">{{ $menu->kategori->nama }}</span>
                <span class="{{ $menu->is_tersedia ? 'badge-tersedia' : 'badge-habis' }}">
                    {{ $menu->is_tersedia ? 'Tersedia' : 'Habis' }}
                </span>
            </div>
            <h4 class="font-bold text-sm text-gray-800 mb-1">{{ $menu->nama }}</h4>
            <p class="text-emerald-600 font-bold text-sm">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
            @if($menu->deskripsi)
                <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ $menu->deskripsi }}</p>
            @endif
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-12 text-gray-400">
        <i class="fa-solid fa-utensils text-4xl mb-3 block"></i>
        <p>Tidak ada menu tersedia</p>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
<div class="mt-6">
    {{ $menus->links() }}
</div>
@endsection

@push('scripts')
<script>
function filterMenu(kategoriId) {
    document.querySelectorAll('.kategori-btn').forEach(b => {
        b.classList.toggle('active', b.dataset.kategori === kategoriId);
    });
    const search = document.getElementById('searchMenu').value;
    const params = new URLSearchParams({ kategori: kategoriId, search: search });
    window.location.href = '{{ route("admin.karyawan.menu") }}?' + params.toString();
}

document.getElementById('searchMenu').addEventListener('keyup', function(e) {
    if (e.key === 'Enter') {
        const activeKat = document.querySelector('.kategori-btn.active');
        const kategoriId = activeKat ? activeKat.dataset.kategori : '';
        const params = new URLSearchParams({ kategori: kategoriId, search: this.value });
        window.location.href = '{{ route("admin.karyawan.menu") }}?' + params.toString();
    }
});
</script>
@endpush
