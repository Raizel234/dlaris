<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pesanan - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; background: #f3f4f6; }
        #app { min-height: 100vh; display: flex; flex-direction: column; }

        .content-wrap { flex: 1; max-width: 1200px; margin: 0 auto; width: 100%; padding: 1.25rem; overflow: hidden; display: flex; flex-direction: column; }

        .status-filter { display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-shrink: 0; overflow-x: auto; padding-bottom: 0.25rem; }
        .filter-btn { padding: 0.5rem 1rem; border-radius: 9999px; font-size: 0.8125rem; font-weight: 600; border: 1px solid #d1d5db; background: #fff; color: #6b7280; cursor: pointer; transition: all 0.2s; white-space: nowrap; }
        .filter-btn:hover { background: #f9fafb; }
        .filter-btn.active { background: #1f2937; color: #fff; border-color: #1f2937; }

        .orders-scroll { flex: 1; overflow-y: auto; padding-right: 0.25rem; display: flex; flex-direction: column; gap: 1rem; }
        .orders-scroll::-webkit-scrollbar { width: 4px; }
        .orders-scroll::-webkit-scrollbar-track { background: transparent; }
        .orders-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 9999px; }

        .order-card { background: #fff; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); overflow: hidden; transition: box-shadow 0.2s; }
        .order-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .order-card .card-body { padding: 1.25rem; }
        .order-card .card-header { display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; margin-bottom: 0.75rem; flex-wrap: wrap; }
        .order-card .order-no { font-weight: 800; font-size: 1.05rem; color: #1f2937; letter-spacing: 0.02em; }
        .order-card .order-meja-wrap { display: flex; align-items: center; gap: 0.35rem; font-size: 0.8125rem; color: #6b7280; }
        .order-card .order-meja-wrap i { color: #9ca3af; }
        .order-card .order-meta { display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 0.75rem; }
        .order-card .order-time { font-size: 0.75rem; color: #9ca3af; display: flex; align-items: center; gap: 0.3rem; }
        .order-card .order-catatan { font-size: 0.75rem; color: #f59e0b; background: #fffbeb; padding: 0.25rem 0.5rem; border-radius: 0.375rem; display: inline-flex; align-items: center; gap: 0.25rem; margin-bottom: 0.75rem; }

        .items-list { border-top: 1px solid #f3f4f6; padding-top: 0.75rem; margin-bottom: 0.75rem; }
        .items-list .item-row { display: flex; justify-content: space-between; align-items: center; padding: 0.25rem 0; font-size: 0.875rem; }
        .items-list .item-row .item-name { color: #374151; }
        .items-list .item-row .item-name .qty { font-weight: 600; color: #1f2937; }
        .items-list .item-row .item-name .catatan { display: block; font-size: 0.7rem; color: #9ca3af; font-style: italic; }
        .items-list .item-row .item-subtotal { color: #6b7280; font-weight: 500; }
        .items-list .item-count { text-align: center; font-size: 0.75rem; color: #9ca3af; padding-top: 0.25rem; }

        .order-total-row { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-top: 2px solid #f3f4f6; margin-bottom: 0.75rem; }
        .order-total-row .label { font-weight: 600; color: #374151; font-size: 0.875rem; }
        .order-total-row .amount { font-weight: 800; color: #1f2937; font-size: 1.125rem; }

        .order-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }

        .badge { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.025em; }
        .badge-menunggu { background: #fef3c7; color: #b45309; }
        .badge-diproses { background: #dbeafe; color: #1d4ed8; }
        .badge-selesai { background: #d1fae5; color: #047857; }
        .badge-dibatalkan { background: #fee2e2; color: #b91c1c; }

        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; text-decoration: none; white-space: nowrap; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-sm { padding: 0.375rem 0.75rem; font-size: 0.75rem; }
        .btn-block { width: 100%; justify-content: center; }
        .btn-primary { background: #3b82f6; color: #fff; }
        .btn-primary:hover:not(:disabled) { background: #2563eb; }
        .btn-success { background: #10b981; color: #fff; }
        .btn-success:hover:not(:disabled) { background: #059669; }
        .btn-warning { background: #f59e0b; color: #fff; }
        .btn-warning:hover:not(:disabled) { background: #d97706; }
        .btn-danger { background: #ef4444; color: #fff; }
        .btn-danger:hover:not(:disabled) { background: #dc2626; }
        .btn-outline { background: transparent; border: 1px solid #d1d5db; color: #374151; }
        .btn-outline:hover:not(:disabled) { background: #f9fafb; }
        .btn-amber { background: #f59e0b; color: #fff; }
        .btn-amber:hover:not(:disabled) { background: #d97706; }

        .empty-state { text-align: center; padding: 4rem 1.5rem; color: #9ca3af; }
        .empty-state i { font-size: 3rem; margin-bottom: 1rem; display: block; color: #d1d5db; }
        .empty-state p { font-size: 0.9375rem; }

        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 50; padding: 1rem; }
        .modal-box { background: #fff; border-radius: 1rem; width: 100%; max-width: 480px; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 60px rgba(0,0,0,0.2); }
        .modal-box .modal-head { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; justify-content: space-between; }
        .modal-box .modal-head h3 { font-size: 1.125rem; font-weight: 800; color: #1f2937; }
        .modal-box .modal-head .close-btn { width: 2rem; height: 2rem; border-radius: 9999px; border: none; background: #f3f4f6; color: #6b7280; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s; }
        .modal-box .modal-head .close-btn:hover { background: #e5e7eb; }
        .modal-box .modal-body { padding: 1.5rem; }

        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-size: 0.8125rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem; }
        .form-control { width: 100%; padding: 0.625rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; outline: none; transition: border-color 0.2s, box-shadow 0.2s; background: #fff; }
        .form-control:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.12); }
        .form-control:disabled { background: #f9fafb; color: #9ca3af; }
        select.form-control { appearance: auto; }
        .input-group { display: flex; align-items: stretch; }
        .input-group .input-prefix { display: flex; align-items: center; padding: 0.625rem 0.75rem; background: #f9fafb; border: 1px solid #d1d5db; border-right: none; border-radius: 0.5rem 0 0 0.5rem; font-size: 0.875rem; font-weight: 600; color: #6b7280; }
        .input-group .form-control { border-radius: 0 0.5rem 0.5rem 0; }

        .kembalian-text { font-size: 0.875rem; font-weight: 600; padding: 0.5rem 0.75rem; border-radius: 0.5rem; margin-top: 0.5rem; }
        .kembalian-text.positive { background: #d1fae5; color: #047857; }
        .kembalian-text.negative { background: #fee2e2; color: #b91c1c; }

        .split-toggle { display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 0; border-top: 1px solid #f3f4f6; margin-top: 1rem; }
        .split-toggle .toggle-label { font-size: 0.8125rem; color: #6b7280; cursor: pointer; user-select: none; }
        .switch { position: relative; width: 2.5rem; height: 1.375rem; flex-shrink: 0; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .switch .slider { position: absolute; inset: 0; background: #d1d5db; border-radius: 9999px; cursor: pointer; transition: background 0.3s; }
        .switch .slider::before { content: ''; position: absolute; left: 0.125rem; bottom: 0.125rem; width: 1.125rem; height: 1.125rem; background: #fff; border-radius: 50%; transition: transform 0.3s; }
        .switch input:checked + .slider { background: #3b82f6; }
        .switch input:checked + .slider::before { transform: translateX(1.125rem); }

        .split-row { display: grid; grid-template-columns: 1fr 1fr auto; gap: 0.5rem; align-items: end; padding: 0.75rem; background: #f9fafb; border-radius: 0.5rem; margin-bottom: 0.5rem; border: 1px solid #f3f4f6; }
        .split-row .form-group { margin-bottom: 0; }
        .split-row .remove-split { width: 2rem; height: 2rem; border-radius: 9999px; border: none; background: #fee2e2; color: #ef4444; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s; flex-shrink: 0; }
        .split-row .remove-split:hover { background: #fecaca; }

        .split-summary { display: flex; justify-content: space-between; align-items: center; font-size: 0.8125rem; padding: 0.5rem 0; color: #6b7280; }
        .split-summary .match { color: #10b981; font-weight: 600; }
        .split-summary .mismatch { color: #ef4444; font-weight: 600; }

        .payment-info { background: #f9fafb; border-radius: 0.5rem; padding: 0.75rem 1rem; margin-bottom: 1rem; }
        .payment-info .info-row { display: flex; justify-content: space-between; font-size: 0.875rem; padding: 0.2rem 0; }
        .payment-info .info-row .info-label { color: #6b7280; }
        .payment-info .info-row .info-value { font-weight: 600; color: #1f2937; }

        @media (min-width: 1024px) {
            .orders-scroll { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; align-content: start; }
            .order-card .card-body { padding: 1.25rem; }
        }

        @media (max-width: 767px) {
            .content-wrap { padding: 0.75rem; }
            .orders-scroll { gap: 0.75rem; }
            .order-card .card-body { padding: 1rem; }
            .order-actions .btn { flex: 1; justify-content: center; }
            .modal-overlay { padding: 0; align-items: flex-end; }
            .modal-overlay .modal-box { max-width: 100%; border-radius: 1rem 1rem 0 0; max-height: 92vh; }
            .split-row { grid-template-columns: 1fr 1fr; }
            .split-row .remove-split { grid-column: 1 / -1; justify-self: end; }
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn 0.3s ease-out both; }
        .fade-in:nth-child(1) { animation-delay: 0.02s; }
        .fade-in:nth-child(2) { animation-delay: 0.04s; }
        .fade-in:nth-child(3) { animation-delay: 0.06s; }
        .fade-in:nth-child(4) { animation-delay: 0.08s; }
        .fade-in:nth-child(5) { animation-delay: 0.1s; }

        .loading-shimmer { background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; border-radius: 0.5rem; }
        @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
    </style>
</head>
<body>
<div id="app"
     x-data="pesananApp()"
     class="h-screen bg-gray-100 overflow-hidden flex flex-col select-none"
     x-init="init()">

    {{-- TOP BAR --}}
    <div class="bg-white border-b border-gray-200 shadow-sm flex-shrink-0 px-4 lg:px-6 py-2.5 flex items-center gap-3">
        <a href="{{ route('admin.pos') }}" class="flex items-center gap-1.5 text-gray-500 hover:text-gray-700 transition-colors mr-1">
            <i class="fa-solid fa-arrow-left text-sm"></i>
            <span class="text-xs font-medium hidden sm:inline">Back</span>
        </a>

        <div class="flex items-center gap-2.5 flex-shrink-0">
            <div class="w-8 h-8 rounded-lg" style="background: #0284c7; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-clipboard-list text-white text-sm"></i>
            </div>
            <span class="font-bold text-sm tracking-wide text-gray-800 hidden sm:inline">D'LARIS POS</span>
        </div>

        <span class="text-gray-300 hidden sm:inline">|</span>

        <div class="flex items-center gap-2 text-sm">
            <i class="fa-regular fa-user text-gray-400"></i>
            <span class="text-gray-600 font-medium truncate max-w-[120px]">{{ Auth::user()->name }}</span>
        </div>

        <div class="flex items-center gap-2 text-sm ml-2">
            <i class="fa-regular fa-clock text-gray-400"></i>
            <span class="text-gray-500 font-mono text-xs font-semibold" x-text="jamSekarang"></span>
        </div>

        <div class="ml-auto flex items-center gap-2">
            <button @click="toggleAbsensi()" :disabled="absensiLoading"
                    :class="absensiStatus?.has_clocked_in ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100'"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors disabled:opacity-50">
                <i class="fa-regular fa-circle-check" :class="absensiStatus?.has_clocked_in ? 'text-emerald-500' : 'text-gray-400'"></i>
                <template x-if="absensiLoading">
                    <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                </template>
                <template x-if="!absensiLoading && absensiStatus?.has_clocked_in">
                    <span x-text="'Clock In (' + absensiStatus.absensi?.jam_masuk + ')'"></span>
                </template>
                <template x-if="!absensiLoading && absensiStatus?.has_clocked_out">
                    <span>| Clock Out</span>
                </template>
                <template x-if="!absensiLoading && !absensiStatus?.has_clocked_in">
                    <span>Clock In</span>
                </template>
            </button>

            <button @click="fetchOrders()" :disabled="ordersLoading" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-gray-600 bg-gray-50 border border-gray-200 hover:bg-gray-100 transition-colors disabled:opacity-50">
                <i class="fa-solid fa-rotate" :class="{'fa-spin': ordersLoading}"></i>
                <span class="hidden sm:inline">Refresh</span>
            </button>

            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-red-600 bg-red-50 border border-red-200 hover:bg-red-100 transition-colors">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="hidden sm:inline">Logout</span>
                </button>
            </form>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="content-wrap">
        {{-- Status Filter --}}
        <div class="status-filter">
            <button @click="statusFilter = 'all'" class="filter-btn" :class="{'active': statusFilter === 'all'}">Semua</button>
            <button @click="statusFilter = 'menunggu'" class="filter-btn" :class="{'active': statusFilter === 'menunggu'}">
                <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500 mr-1"></span>
                Menunggu
            </button>
            <button @click="statusFilter = 'diproses'" class="filter-btn" :class="{'active': statusFilter === 'diproses'}">
                <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-500 mr-1"></span>
                Diproses
            </button>
            <button @click="statusFilter = 'selesai'" class="filter-btn" :class="{'active': statusFilter === 'selesai'}">
                <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1"></span>
                Selesai
            </button>
        </div>

        {{-- Orders List --}}
        <div class="orders-scroll">
            <template x-if="ordersLoading && filteredOrders.length === 0">
                <div class="empty-state">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    <p>Memuat pesanan...</p>
                </div>
            </template>

            <template x-if="!ordersLoading && filteredOrders.length === 0">
                <div class="empty-state">
                    <i class="fa-solid fa-clipboard-list"></i>
                    <p>Tidak ada pesanan</p>
                </div>
            </template>

            <template x-for="(order, idx) in filteredOrders" :key="order.id">
                <div class="order-card fade-in" :style="'animation-delay:' + (idx * 0.03) + 's'">
                    <div class="card-body">
                        {{-- Header --}}
                        <div class="card-header">
                            <div>
                                <div class="order-no" x-text="order.nomor_order"></div>
                                <div class="order-meja-wrap" x-show="order.meja">
                                    <i class="fa-solid fa-chair"></i>
                                    <span x-text="'Meja ' + order.meja.nomor_meja"></span>
                                </div>
                                <div class="order-meja-wrap" x-show="!order.meja &amp;&amp; order.tipe_pesanan">
                                    <i class="fa-solid fa-bag-shopping"></i>
                                    <span x-text="order.tipe_pesanan === 'takeaway' ? 'Take Away' : order.tipe_pesanan === 'delivery' ? 'Delivery' : ''"></span>
                                </div>
                                <div class="order-meja-wrap" x-show="order.nama_pelanggan" style="margin-top:2px;">
                                    <i class="fa-solid fa-user"></i>
                                    <span x-text="order.nama_pelanggan"></span>
                                    <template x-if="order.no_hp">
                                        <span x-text="'(' + order.no_hp + ')'" style="color:#9ca3af;font-weight:400;"></span>
                                    </template>
                                </div>
                                <div class="order-meja-wrap" x-show="order.tipe_pesanan === 'delivery' &amp;&amp; order.alamat_pengiriman" style="margin-top:2px;">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <span x-text="order.alamat_pengiriman" style="font-size:.7rem;color:#6b7280;"></span>
                                </div>
                                <div class="order-meja-wrap" x-show="order.ongkir > 0" style="margin-top:2px;">
                                    <i class="fa-solid fa-truck"></i>
                                    <span x-text="'Ongkir: Rp ' + formatNum(order.ongkir)" style="font-size:.7rem;color:#f59e0b;"></span>
                                </div>
                            </div>
                            <span class="badge" :class="'badge-' + order.status" x-text="order.status"></span>
                        </div>

                        {{-- Meta --}}
                        <div class="order-meta">
                            <span class="order-time">
                                <i class="fa-regular fa-clock"></i>
                                <span x-text="timeAgo(order.created_at)"></span>
                            </span>
                        </div>

                        {{-- Catatan --}}
                        <div class="order-catatan" x-show="order.catatan">
                            <i class="fa-solid fa-note-sticky"></i>
                            <span x-text="order.catatan"></span>
                        </div>

                        {{-- Items --}}
                        <div class="items-list">
                            <template x-for="(item, i) in order.items" :key="item.id">
                                <div class="item-row" x-show="i < 9 || order._showAll">
                                    <div class="item-name">
                                        <span class="qty" x-text="item.jumlah + 'x'"></span>
                        <span x-text="item.menu?.nama || 'Menu#' + item.menu_id"></span>
                                        <span class="catatan" x-show="item.catatan" x-text="'— ' + item.catatan"></span>
                                    </div>
                                    <span class="item-subtotal">Rp <span x-text="formatNum(item.jumlah * item.harga)"></span></span>
                                </div>
                            </template>
                            <div class="item-count" x-show="order.items.length > 9 && !order._showAll">
                                <button @click="order._showAll = true" class="text-blue-600 hover:text-blue-800 text-xs font-medium bg-transparent border-none cursor-pointer">
                                    + <span x-text="order.items.length - 9"></span> item lainnya
                                </button>
                            </div>
                        </div>

                        {{-- Total --}}
                        <div class="order-total-row">
                            <span class="label">Total</span>
                            <span class="amount">Rp <span x-text="formatNum(order.grand_total > 0 ? order.grand_total : order.total)"></span></span>
                        </div>

                        {{-- Actions --}}
                        <div class="order-actions">
                            <template x-if="order.status === 'menunggu'">
                                <button @click="updateStatus(order.id, 'diproses')" class="btn btn-primary btn-sm flex-1">
                                    <i class="fa-solid fa-check"></i> Proses
                                </button>
                            </template>
                            <template x-if="order.status === 'diproses'">
                                <button @click="updateStatus(order.id, 'selesai')" class="btn btn-success btn-sm flex-1">
                                    <i class="fa-solid fa-check"></i> Selesai
                                </button>
                            </template>
                            <template x-if="order.status === 'selesai'">
                                <button @click="openPaymentModal(order)" class="btn btn-warning btn-sm flex-1">
                                    <i class="fa-solid fa-money-bill-wave"></i> Bayar
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- PAYMENT MODAL --}}
    <template x-if="showPaymentModal">
        <div class="modal-overlay" @click.self="closePaymentModal()">
            <div class="modal-box">
                <div class="modal-head">
                    <h3><i class="fa-solid fa-money-bill-wave text-amber-500 mr-2"></i>Pembayaran</h3>
                    <button @click="closePaymentModal()" class="close-btn"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body">
                    {{-- Order Info --}}
                    <div class="payment-info">
                        <div class="info-row">
                            <span class="info-label">No. Order</span>
                            <span class="info-value" x-text="paymentOrder?.nomor_order"></span>
                        </div>
                        <div class="info-row" x-show="paymentOrder?.meja">
                            <span class="info-label">Meja</span>
                            <span class="info-value" x-text="'Meja ' + paymentOrder?.meja?.nomor_meja"></span>
                        </div>
                        <div class="info-row" style="border-top:1px solid #e5e7eb;padding-top:0.5rem;margin-top:0.25rem;">
                            <span class="info-label font-bold" style="color:#1f2937;">Total</span>
                            <span class="info-value font-bold" style="color:#d97706;font-size:1.125rem;">Rp <span x-text="formatNum(paymentOrder?.grand_total > 0 ? paymentOrder?.grand_total : paymentOrder?.total)"></span></span>
                        </div>
                        <div class="info-row" x-show="paymentOrder?.items">
                            <span class="info-label">Items</span>
                            <span class="info-value" x-text="paymentOrder?.items?.length + ' item'"></span>
                        </div>
                    </div>

                    {{-- Single Payment --}}
                    <template x-if="!showSplitPayment">
                        <form @submit.prevent="submitPayment()">
                            <div class="form-group">
                                <label>Metode Pembayaran</label>
                                <select x-model="paymentForm.metode_bayar" class="form-control" @change="onMetodeChange()">
                                    <option value="tunai">Tunai</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="qris">QRIS</option>
                                </select>
                            </div>

                            <template x-if="paymentForm.metode_bayar === 'tunai'">
                                <div class="form-group">
                                    <label>Nominal Bayar</label>
                                    <div class="input-group">
                                        <span class="input-prefix">Rp</span>
                                        <input type="number" x-model.number="paymentForm.nominal_bayar" min="0"
                                               class="form-control" placeholder="0"
                                               @input.debounce.300ms="calculateKembalian()">
                                    </div>
                                    <div class="kembalian-text" :class="kembalianClass" x-show="paymentForm.kembalian !== null">
                                        <span x-text="kembalianLabel"></span>
                                    </div>
                                </div>
                            </template>

                            <div class="flex gap-3 mt-4">
                                <button type="button" @click="closePaymentModal()" class="btn btn-outline flex-1">Batal</button>
                                <button type="submit" class="btn btn-amber flex-1" :disabled="payLoading || !paymentValid">
                                    <i class="fa-solid fa-spinner fa-spin" x-show="payLoading"></i>
                                    <i class="fa-solid fa-check" x-show="!payLoading"></i>
                                    <span x-text="payLoading ? 'Memproses...' : 'Bayar Sekarang'"></span>
                                </button>
                            </div>
                        </form>
                    </template>

                    {{-- Split Payment Toggle --}}
                    <div class="split-toggle">
                        <label class="switch">
                            <input type="checkbox" x-model="showSplitPayment" @change="onSplitToggle()">
                            <span class="slider"></span>
                        </label>
                        <span class="toggle-label">Split Payment</span>
                    </div>

                    {{-- Split Payment Form --}}
                    <template x-if="showSplitPayment">
                        <div>
                            <template x-for="(sp, idx) in splitPayments" :key="idx">
                                <div class="split-row">
                                    <div class="form-group">
                                        <label>Metode</label>
                                        <select x-model="sp.metode_bayar" class="form-control">
                                            <option value="tunai">Tunai</option>
                                            <option value="transfer">Transfer</option>
                                            <option value="qris">QRIS</option>
                                            <option value="kartu">Kartu</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Jumlah (Rp)</label>
                                        <input type="number" x-model.number="sp.jumlah" min="0" class="form-control" placeholder="0" @input="recalculateSplitTotal()">
                                    </div>
                                    <button type="button" @click="removeSplitPayment(idx)" class="remove-split" x-show="splitPayments.length > 2">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            </template>

                            <div class="split-summary">
                                <span>Total dibayar:</span>
                                <span :class="splitTotal === getTotal(paymentOrder) ? 'match' : 'mismatch'">
                                    Rp <span x-text="formatNum(splitTotal)"></span>
                                    <span x-text="' / Rp ' + formatNum(getTotal(paymentOrder))"></span>
                                </span>
                            </div>

                            <button type="button" @click="addSplitPayment()" class="btn btn-outline btn-sm btn-block mb-3" x-show="splitPayments.length < 4">
                                <i class="fa-solid fa-plus"></i> Tambah Metode
                            </button>

                            <div class="flex gap-3 mt-2">
                                <button type="button" @click="closePaymentModal()" class="btn btn-outline flex-1">Batal</button>
                                <button type="button" @click="submitSplitPayment()" class="btn btn-amber flex-1" :disabled="payLoading || !splitValid">
                                    <i class="fa-solid fa-spinner fa-spin" x-show="payLoading"></i>
                                    <i class="fa-solid fa-check" x-show="!payLoading"></i>
                                    <span x-text="payLoading ? 'Memproses...' : 'Bayar Split'"></span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function pesananApp() {
    return {
        orders: [],
        statusFilter: 'all',
        ordersLoading: false,
        notifTotal: 0,
        jamSekarang: '',
        clockInterval: null,
        pollingInterval: null,
        absensiStatus: null,
        absensiLoading: false,

        showPaymentModal: false,
        paymentOrder: null,
        paymentForm: { metode_bayar: 'tunai', nominal_bayar: 0, kembalian: null },
        showSplitPayment: false,
        splitPayments: [],
        payLoading: false,

        init() {
            this.updateJamSekarang();
            this.clockInterval = setInterval(() => { this.updateJamSekarang(); }, 1000);
            this.cekAbsensi();
            this.fetchOrders();
            this.pollingInterval = setInterval(() => {
                this.fetchOrders();
            }, 10000);
        },
        destroy() {
            if (this.clockInterval) clearInterval(this.clockInterval);
            if (this.pollingInterval) clearInterval(this.pollingInterval);
        },

        formatNum(n) {
            return new Intl.NumberFormat('id-ID').format(n || 0);
        },

        updateJamSekarang() {
            const now = new Date();
            this.jamSekarang = now.toLocaleTimeString('id-ID', { hour12: false });
        },

        timeAgo(dateStr) {
            if (!dateStr) return '';
            const now = new Date();
            const date = new Date(dateStr);
            const diff = Math.floor((now - date) / 1000);
            if (diff < 60) return 'Baru saja';
            if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
            if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
        },

        get filteredOrders() {
            if (this.statusFilter === 'all') return this.orders;
            return this.orders.filter(o => o.status === this.statusFilter);
        },

        fetchOrders() {
            this.ordersLoading = true;
            axios.get('{{ route("admin.pos.orders") }}')
                .then(res => {
                    if (res.data.success) {
                        const data = res.data.data || [];
                        data.forEach(o => { o._showAll = false; });
                        this.orders = data;
                    }
                })
                .catch(() => {})
                .finally(() => { this.ordersLoading = false; });
        },

        updateStatus(id, status) {
            const statusLabels = { diproses: 'Proses pesanan?', selesai: 'Tandai selesai?' };
            const statusTexts = { diproses: 'Pesanan akan diproses oleh dapur', selesai: 'Pesanan siap disajikan' };
            const statusIcons = { diproses: 'info', selesai: 'success' };

            Swal.fire({
                title: statusLabels[status] || 'Ubah status?',
                text: statusTexts[status] || '',
                icon: statusIcons[status] || 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, ' + (status === 'diproses' ? 'Proses' : 'Selesai'),
                cancelButtonText: 'Batal',
                confirmButtonColor: status === 'diproses' ? '#3b82f6' : '#10b981',
                background: '#fff',
                color: '#374151',
            }).then(r => {
                if (!r.isConfirmed) return;
                axios.patch('{{ url("admin/pos/order") }}/' + id + '/status', { status })
                    .then(res => {
                        if (res.data.success) {
                            Swal.fire({ icon: 'success', title: 'Berhasil', text: res.data.message || 'Status berhasil diubah', timer: 1500, showConfirmButton: false, background: '#fff', color: '#374151', toast: true, position: 'top-end' });
                            this.fetchOrders();
                        }
                    })
                    .catch(err => {
                        const msg = err.response?.data?.message || 'Gagal mengubah status';
                        Swal.fire({ icon: 'error', title: 'Gagal', text: msg, background: '#fff', color: '#374151' });
                    });
            });
        },

        openPaymentModal(order) {
            this.paymentOrder = order;
            const total = order.grand_total > 0 ? order.grand_total : order.total;
            this.paymentForm = { metode_bayar: 'tunai', nominal_bayar: total, kembalian: 0 };
            this.showSplitPayment = false;
            this.splitPayments = [
                { metode_bayar: 'tunai', jumlah: Math.round(total / 2), nominal_bayar: Math.round(total / 2) },
                { metode_bayar: 'transfer', jumlah: Math.ceil(total / 2), nominal_bayar: Math.ceil(total / 2) },
            ];
            this.showPaymentModal = true;
            this.calculateKembalian();
        },
        closePaymentModal() {
            this.showPaymentModal = false;
            this.paymentOrder = null;
            this.payLoading = false;
        },

        onMetodeChange() {
            const total = this.getTotal(this.paymentOrder);
            if (this.paymentForm.metode_bayar !== 'tunai') {
                this.paymentForm.nominal_bayar = total;
                this.paymentForm.kembalian = 0;
            } else {
                this.paymentForm.nominal_bayar = total;
                this.calculateKembalian();
            }
        },

        getTotal(order) {
            return order ? (order.grand_total > 0 ? order.grand_total : order.total) : 0;
        },

        calculateKembalian() {
            const total = this.getTotal(this.paymentOrder);
            const bayar = this.paymentForm.nominal_bayar || 0;
            if (this.paymentForm.metode_bayar !== 'tunai') {
                this.paymentForm.kembalian = 0;
                return;
            }
            if (bayar < total) {
                this.paymentForm.kembalian = -(total - bayar);
                return;
            }
            axios.post('{{ route("admin.pos.hitung-kembalian") }}', {
                total: total,
                nominal_bayar: bayar,
            }).then(res => {
                if (res.data.success) {
                    this.paymentForm.kembalian = res.data.data.kembalian;
                }
            }).catch(err => {
                const msg = err.response?.data?.message || '';
                if (msg.includes('kurang')) {
                    this.paymentForm.kembalian = -(total - bayar);
                } else {
                    this.paymentForm.kembalian = bayar - total;
                }
            });
        },

        get kembalianClass() {
            const k = this.paymentForm.kembalian;
            if (k === null || k === undefined) return '';
            return k >= 0 ? 'positive' : 'negative';
        },
        get kembalianLabel() {
            const k = this.paymentForm.kembalian;
            if (k === null || k === undefined) return '';
            if (k >= 0) return 'Kembalian: Rp ' + this.formatNum(k);
            return 'Kurang: Rp ' + this.formatNum(Math.abs(k));
        },
        get paymentValid() {
            const total = this.paymentOrder ? (this.paymentOrder.grand_total > 0 ? this.paymentOrder.grand_total : this.paymentOrder.total) : 0;
            if (this.paymentForm.metode_bayar === 'tunai') {
                return (this.paymentForm.nominal_bayar || 0) >= total;
            }
            return true;
        },

        submitPayment() {
            if (this.payLoading || !this.paymentValid) return;
            this.payLoading = true;
            const total = this.getTotal(this.paymentOrder);
            axios.post('{{ url("admin/pos/order") }}/' + this.paymentOrder.id + '/bayar', {
                metode_bayar: this.paymentForm.metode_bayar,
                nominal_bayar: this.paymentForm.metode_bayar === 'tunai'
                    ? (this.paymentForm.nominal_bayar || 0)
                    : total,
            }).then(res => {
                if (res.data.success) {
                    const d = res.data.data;
                    Swal.fire({
                        icon: 'success',
                        title: 'Pembayaran Berhasil!',
                        html: `
                            <div class="text-left text-sm space-y-1" style="text-align:left;">
                                <p>Kode: <strong>${d.kode_transaksi}</strong></p>
                                <p>Total: <strong>Rp ${new Intl.NumberFormat('id-ID').format(d.total)}</strong></p>
                                <p>Metode: <strong class="capitalize">${d.metode_bayar}</strong></p>
                            </div>
                        `,
                        confirmButtonText: '<i class="fa-solid fa-print mr-1"></i> Cetak Struk',
                        showCancelButton: true,
                        cancelButtonText: 'Selesai',
                        confirmButtonColor: '#10b981',
                        background: '#fff',
                        color: '#374151',
                    }).then(r => {
                        if (r.isConfirmed) {
                            window.open('{{ url("admin/pos/order") }}/' + d.id + '/cetak', '_blank');
                        }
                        this.closePaymentModal();
                        this.fetchOrders();
                    });
                }
            }).catch(err => {
                const msg = err.response?.data?.message || 'Terjadi kesalahan server';
                Swal.fire({ icon: 'error', title: 'Gagal', text: msg, background: '#fff', color: '#374151' });
            }).finally(() => { this.payLoading = false; });
        },

        onSplitToggle() {
            if (!this.showSplitPayment) return;
            if (this.splitPayments.length === 0) {
                const total = this.getTotal(this.paymentOrder);
                this.splitPayments = [
                    { metode_bayar: 'tunai', jumlah: Math.round(total / 2), nominal_bayar: Math.round(total / 2) },
                    { metode_bayar: 'transfer', jumlah: Math.ceil(total / 2), nominal_bayar: Math.ceil(total / 2) },
                ];
            }
        },

        addSplitPayment() {
            if (this.splitPayments.length >= 4) return;
            this.splitPayments.push({ metode_bayar: 'tunai', jumlah: 0, nominal_bayar: 0 });
        },

        removeSplitPayment(idx) {
            if (this.splitPayments.length <= 2) return;
            this.splitPayments.splice(idx, 1);
            this.recalculateSplitTotal();
        },

        recalculateSplitTotal() {
            // just trigger reactivity
            this.splitPayments = [...this.splitPayments];
        },

        get splitTotal() {
            return (this.splitPayments || []).reduce((sum, sp) => sum + (sp.jumlah || 0), 0);
        },

        get splitValid() {
            const total = this.getTotal(this.paymentOrder);
            const sum = this.splitTotal;
            if (sum === 0) return false;
            return Math.abs(sum - total) <= 100;
        },

        submitSplitPayment() {
            if (this.payLoading || !this.splitValid) return;
            this.payLoading = true;
            const payments = this.splitPayments.map(sp => ({
                metode_bayar: sp.metode_bayar,
                jumlah: sp.jumlah || 0,
                nominal_bayar: sp.jumlah || 0,
            }));
            axios.post('{{ url("admin/pos/order") }}/' + this.paymentOrder.id + '/split-bayar', { payments })
                .then(res => {
                    if (res.data.success) {
                        const d = res.data.data;
                        Swal.fire({
                            icon: 'success',
                            title: 'Pembayaran Split Berhasil!',
                            html: `
                                <div class="text-left text-sm space-y-1" style="text-align:left;">
                                    <p>Kode: <strong>${d.kode_transaksi}</strong></p>
                                    <p>Total: <strong>Rp ${new Intl.NumberFormat('id-ID').format(d.total)}</strong></p>
                                    <p>Metode: <strong>Split (${(d.split_payments || []).length} metode)</strong></p>
                                </div>
                            `,
                            confirmButtonText: '<i class="fa-solid fa-print mr-1"></i> Cetak Struk',
                            showCancelButton: true,
                            cancelButtonText: 'Selesai',
                            confirmButtonColor: '#10b981',
                            background: '#fff',
                            color: '#374151',
                        }).then(r => {
                            if (r.isConfirmed) {
                                window.open('{{ url("admin/pos/order") }}/' + d.id + '/cetak', '_blank');
                            }
                            this.closePaymentModal();
                            this.fetchOrders();
                        });
                    }
                })
                .catch(err => {
                    const msg = err.response?.data?.message || 'Terjadi kesalahan server';
                    Swal.fire({ icon: 'error', title: 'Gagal', text: msg, background: '#fff', color: '#374151' });
                })
                .finally(() => { this.payLoading = false; });
        },

        cekAbsensi() {
            axios.get('{{ route("admin.absensi.cek-status") }}')
                .then(res => { this.absensiStatus = res.data.data; })
                .catch(() => {});
        },

        toggleAbsensi() {
            if (this.absensiLoading) return;
            this.absensiLoading = true;
            if (this.absensiStatus?.has_clocked_in && !this.absensiStatus?.has_clocked_out) {
                axios.post('{{ route("admin.absensi.clock-out") }}')
                    .then(res => {
                        this.cekAbsensi();
                        Swal.fire({ icon: 'success', title: 'Clock Out Berhasil', text: 'Jam pulang tercatat', timer: 2000, showConfirmButton: false, background: '#fff', color: '#374151', toast: true, position: 'top-end' });
                    })
                    .catch(err => {
                        const msg = err.response?.data?.message || 'Gagal clock out';
                        Swal.fire({ icon: 'error', title: 'Gagal', text: msg, background: '#fff', color: '#374151' });
                    })
                    .finally(() => { this.absensiLoading = false; });
            } else {
                axios.post('{{ route("admin.absensi.clock-in") }}')
                    .then(res => {
                        this.cekAbsensi();
                        Swal.fire({ icon: 'success', title: 'Clock In Berhasil!', text: 'Selamat bekerja!', timer: 2000, showConfirmButton: false, background: '#fff', color: '#374151', toast: true, position: 'top-end' });
                    })
                    .catch(err => {
                        const msg = err.response?.data?.message || 'Gagal clock in';
                        Swal.fire({ icon: 'error', title: 'Gagal', text: msg, background: '#fff', color: '#374151' });
                    })
                    .finally(() => { this.absensiLoading = false; });
            }
        },
    };
}
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
