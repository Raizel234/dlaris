<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Karaoke - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; background: #f3f4f6; }
        #app { min-height: 100vh; display: flex; flex-direction: column; }

        .content-wrap { display: flex; gap: 1.5rem; padding: 1.5rem; flex: 1; max-width: 1400px; margin: 0 auto; width: 100%; }

        .booking-panel { flex: 0 0 40%; min-width: 0; }
        .sessions-panel { flex: 1; min-width: 0; }

        .panel-card { background: #fff; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); overflow: hidden; }
        .panel-card .panel-head { padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 0.75rem; }
        .panel-card .panel-head i { font-size: 1rem; width: 1.5rem; text-align: center; }
        .panel-card .panel-head h3 { font-size: 1rem; font-weight: 700; color: #1f2937; }
        .panel-card .panel-body { padding: 1.25rem; }

        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-size: 0.8125rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem; }
        .form-control { width: 100%; padding: 0.625rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; outline: none; transition: border-color 0.2s, box-shadow 0.2s; background: #fff; }
        .form-control:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,0.12); }
        textarea.form-control { resize: vertical; min-height: 70px; }

        .price-preview { background: linear-gradient(135deg, #f5f3ff, #ede9fe); border: 1px solid #c4b5fd; border-radius: 0.75rem; padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; }
        .price-preview .label { font-size: 0.8rem; color: #6d28d9; font-weight: 600; }
        .price-preview .amount { font-size: 1.5rem; font-weight: 800; color: #5b21b6; }

        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.625rem 1.25rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; text-decoration: none; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-primary { background: #7c3aed; color: #fff; }
        .btn-primary:hover:not(:disabled) { background: #6d28d9; }
        .btn-success { background: #10b981; color: #fff; }
        .btn-success:hover:not(:disabled) { background: #059669; }
        .btn-warning { background: #f59e0b; color: #fff; }
        .btn-warning:hover:not(:disabled) { background: #d97706; }
        .btn-outline { background: transparent; border: 1px solid #d1d5db; color: #374151; }
        .btn-outline:hover:not(:disabled) { background: #f9fafb; }
        .btn-sm { padding: 0.375rem 0.75rem; font-size: 0.75rem; }
        .btn-block { width: 100%; }

        .session-card { border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem 1.25rem; margin-bottom: 1rem; transition: box-shadow 0.2s; }
        .session-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .session-card .top-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem; margin-bottom: 0.5rem; }
        .session-card .room-name { font-weight: 700; color: #1f2937; font-size: 1rem; }
        .session-card .customer-name { font-size: 0.8125rem; color: #6b7280; }
        .session-card .customer-name i { margin-right: 0.25rem; }

        .badge { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.025em; }
        .badge-confirmed { background: #dbeafe; color: #1d4ed8; }
        .badge-ongoing { background: #d1fae5; color: #047857; }
        .badge-expired { background: #fee2e2; color: #b91c1c; }

        .timer-display { font-family: 'Courier New', Courier, monospace; font-weight: 700; font-size: 1.35rem; color: #1f2937; letter-spacing: 0.05em; }

        .progress-track { width: 100%; height: 6px; background: #e5e7eb; border-radius: 9999px; overflow: hidden; margin: 0.5rem 0 0.75rem; }
        .progress-fill { height: 100%; border-radius: 9999px; background: linear-gradient(90deg, #7c3aed, #10b981); transition: width 1s linear; }

        .session-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.5rem; }

        .empty-state { text-align: center; padding: 3rem 1.5rem; color: #9ca3af; }
        .empty-state i { font-size: 2.5rem; margin-bottom: 0.75rem; display: block; }
        .empty-state p { font-size: 0.875rem; }

        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; z-index: 50; padding: 1rem; }
        .modal-box { background: #fff; border-radius: 1rem; padding: 1.5rem; width: 100%; max-width: 400px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
        .modal-box h3 { font-size: 1.125rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem; }

        .sessions-scroll { max-height: calc(100vh - 200px); overflow-y: auto; padding-right: 0.25rem; }
        .sessions-scroll::-webkit-scrollbar { width: 4px; }
        .sessions-scroll::-webkit-scrollbar-track { background: transparent; }
        .sessions-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 9999px; }

        .btn-purple { background: #7c3aed; color: #fff; }
        .btn-purple:hover:not(:disabled) { background: #6d28d9; }

        @media (max-width: 1023px) {
            .content-wrap { flex-direction: column; padding: 1rem; gap: 1rem; }
            .booking-panel { flex: none; }
        }

        @media (max-width: 767px) {
            .content-wrap { padding: 0.75rem; gap: 0.75rem; }
            .panel-card .panel-head { padding: 0.75rem 1rem; }
            .panel-card .panel-body { padding: 1rem; }
            .timer-display { font-size: 1.1rem; }
            .session-card { padding: 0.75rem 1rem; }
            .sessions-scroll { max-height: none; }
        }
    </style>
</head>
<body>
<div id="app"
     x-data="karaokeApp()"
     class="h-screen bg-gray-100 overflow-hidden flex flex-col select-none"
     x-init="init()">

    {{-- TOP BAR --}}
    <div class="bg-white border-b border-gray-200 shadow-sm flex-shrink-0 px-4 lg:px-6 py-2.5 flex items-center gap-3">
        <a href="{{ route('admin.pos') }}" class="flex items-center gap-1.5 text-gray-500 hover:text-gray-700 transition-colors mr-1">
            <i class="fa-solid fa-arrow-left text-sm"></i>
            <span class="text-xs font-medium hidden sm:inline">Back</span>
        </a>

        <div class="flex items-center gap-2.5 flex-shrink-0">
            <div class="w-8 h-8 rounded-lg" style="background: #7c3aed; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-microphone text-white text-sm"></i>
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
        {{-- LEFT: BOOKING FORM --}}
        <div class="booking-panel">
            <div class="panel-card">
                <div class="panel-head" style="border-left: 4px solid #7c3aed;">
                    <i class="fa-solid fa-pen-to-square" style="color:#7c3aed;"></i>
                    <h3>Booking Karaoke</h3>
                </div>
                <div class="panel-body">
                    <form @submit.prevent="bookingSubmit()">
                        <div class="form-group">
                            <label for="ruangan_id">Ruangan</label>
                            <select id="ruangan_id" x-model="bookingForm.ruangan_id" class="form-control">
                                <option value="">-- Pilih Ruangan --</option>
                                <template x-for="r in ruangans" :key="r.id">
                                    <option :value="r.id" x-text="r.nama + ' (Rp ' + formatNum(r.tarif_per_jam) + '/jam)'" :disabled="r.status === 'maintenance'"></option>
                                </template>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nama_pemesan">Nama Pemesan</label>
                            <input id="nama_pemesan" type="text" x-model="bookingForm.nama_pemesan" class="form-control" placeholder="Nama customer">
                        </div>

                        <div class="form-group">
                            <label for="nomor_hp">No. HP</label>
                            <input id="nomor_hp" type="text" x-model="bookingForm.nomor_hp" class="form-control" placeholder="0812xxxxxxx">
                        </div>

                        <div class="form-group">
                            <label for="durasi">Durasi (Jam)</label>
                            <input id="durasi" type="number" x-model.number="bookingForm.durasi" min="1" max="6" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="catatan">Catatan</label>
                            <textarea id="catatan" x-model="bookingForm.catatan" class="form-control" placeholder="Catatan (opsional)"></textarea>
                        </div>

                        <div class="price-preview mb-4">
                            <span class="label">Total Harga</span>
                            <span class="amount">Rp <span x-text="formatNum(hargaPreview)"></span></span>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block" :disabled="!formValid || bookingLoading">
                            <i class="fa-solid fa-microphone" x-show="!bookingLoading"></i>
                            <i class="fa-solid fa-spinner fa-spin" x-show="bookingLoading"></i>
                            <span x-text="bookingLoading ? 'Memproses...' : 'Booking Sekarang'"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- RIGHT: ACTIVE SESSIONS --}}
        <div class="sessions-panel">
            <div class="panel-card">
                <div class="panel-head" style="border-left: 4px solid #10b981;">
                    <i class="fa-solid fa-list" style="color:#10b981;"></i>
                    <h3>Sesi Aktif</h3>
                    <button @click="fetchActiveBookings()" class="btn btn-outline btn-sm ml-auto" :disabled="bookingLoading">
                        <i class="fa-solid fa-rotate" :class="{'fa-spin': bookingLoading}"></i>
                        <span class="hidden sm:inline">Refresh</span>
                    </button>
                </div>
                <div class="panel-body sessions-scroll">
                    <template x-if="activeBookings.length === 0">
                        <div class="empty-state">
                            <i class="fa-solid fa-microphone-slash"></i>
                            <p>Tidak ada sesi karaoke aktif</p>
                        </div>
                    </template>
                    <template x-for="b in activeBookings" :key="b.id">
                        <div class="session-card">
                            <div class="top-row">
                                <div>
                                    <div class="room-name">
                                        <i class="fa-solid fa-door-open text-purple-500" style="color:#7c3aed;margin-right:0.35rem;"></i>
                                        <span x-text="b.ruangan.nama"></span>
                                    </div>
                                    <div class="customer-name">
                                        <i class="fa-regular fa-user"></i>
                                        <span x-text="b.nama_pemesan"></span>
                                        <span x-text="'(' + b.nomor_hp + ')'" class="text-gray-400 ml-1"></span>
                                    </div>
                                </div>
                                <div>
                                    <span class="badge" :class="{
                                        'badge-confirmed': b.status === 'confirmed',
                                        'badge-ongoing': b.status === 'ongoing',
                                        'badge-expired': timerData[b.id]?.is_expired
                                    }" x-text="timerData[b.id]?.is_expired ? 'Expired' : (b.status === 'ongoing' ? 'Ongoing' : 'Confirmed')"></span>
                                </div>
                            </div>

                            <div x-show="b.status === 'ongoing' || timerData[b.id]?.is_expired">
                                <div class="flex items-center justify-between">
                                    <span class="timer-display" x-text="timerData[b.id]?.display || '00:00:00'"></span>
                                </div>
                                <div class="progress-track">
                                    <div class="progress-fill" :style="'width:' + (timerData[b.id]?.progress || 0) + '%'"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-400">
                                    <span x-text="b.jam_mulai"></span>
                                    <span x-text="b.jam_selesai"></span>
                                </div>
                            </div>

                            <div class="session-actions">
                                <template x-if="b.status === 'confirmed'">
                                    <button @click="startSession(b.id)" class="btn btn-success btn-sm">
                                        <i class="fa-solid fa-play"></i> Mulai
                                    </button>
                                </template>
                                <template x-if="b.status === 'ongoing' && !timerData[b.id]?.is_expired">
                                    <button @click="openExtendModal(b)" class="btn btn-warning btn-sm">
                                        <i class="fa-solid fa-clock"></i> Perpanjang
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- EXTEND MODAL --}}
    <template x-if="showExtendModal">
        <div class="modal-overlay" @click.self="showExtendModal = false">
            <div class="modal-box">
                <h3><i class="fa-solid fa-clock"></i> Perpanjang Sesi</h3>
                <p class="text-sm text-gray-600 mb-3" x-text="'Tambahan waktu untuk booking #' + extendForm.booking_id"></p>
                <div class="form-group">
                    <label>Tambahan Jam</label>
                    <input type="number" x-model.number="extendForm.tambah_jam" min="1" max="6" class="form-control">
                </div>
                <div class="flex gap-3 mt-4">
                    <button @click="showExtendModal = false" class="btn btn-outline flex-1">Batal</button>
                    <button @click="extendSession()" class="btn btn-purple flex-1" :disabled="bookingLoading">
                        <i class="fa-solid fa-check" x-show="!bookingLoading"></i>
                        <i class="fa-solid fa-spinner fa-spin" x-show="bookingLoading"></i>
                        Konfirmasi
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function karaokeApp() {
    return {
        ruangans: @json($ruangans),
        bookingForm: { ruangan_id: '', nama_pemesan: '', nomor_hp: '', durasi: 2, catatan: '' },
        bookingLoading: false,
        activeBookings: [],
        timerData: {},
        showExtendModal: false,
        extendForm: { booking_id: null, tambah_jam: 1 },
        timerInterval: null,
        jamSekarang: '',
        clockInterval: null,
        absensiStatus: null,
        absensiLoading: false,
        pollingInterval: null,

        init() {
            this.updateJamSekarang();
            this.clockInterval = setInterval(() => { this.updateJamSekarang(); }, 1000);
            this.cekAbsensi();
            this.fetchActiveBookings();
            this.pollingInterval = setInterval(() => { this.fetchActiveBookings(); }, 30000);
        },
        destroy() {
            if (this.clockInterval) clearInterval(this.clockInterval);
            if (this.timerInterval) clearInterval(this.timerInterval);
            if (this.pollingInterval) clearInterval(this.pollingInterval);
        },

        formatNum(n) {
            return new Intl.NumberFormat('id-ID').format(n || 0);
        },

        updateJamSekarang() {
            const now = new Date();
            this.jamSekarang = now.toLocaleTimeString('id-ID', { hour12: false });
        },

        get hargaPreview() {
            if (!this.bookingForm.ruangan_id) return 0;
            const r = this.ruangans.find(r => r.id == this.bookingForm.ruangan_id);
            if (!r) return 0;
            return (r.tarif_per_jam || 0) * (this.bookingForm.durasi || 0);
        },

        get formValid() {
            const f = this.bookingForm;
            return f.ruangan_id && f.nama_pemesan.trim() && f.nomor_hp.trim() && f.durasi >= 1 && f.durasi <= 6;
        },

        bookingSubmit() {
            if (!this.formValid || this.bookingLoading) return;
            this.bookingLoading = true;
            axios.post('{{ route("admin.pos.booking-karaoke") }}', this.bookingForm)
                .then(res => {
                    if (res.data.success) {
                        Swal.fire({ icon: 'success', title: 'Booking Berhasil', text: res.data.message || 'Sesi karaoke berhasil di-book', timer: 2000, showConfirmButton: false, background: '#fff', color: '#374151', toast: true, position: 'top-end' });
                        this.bookingForm = { ruangan_id: '', nama_pemesan: '', nomor_hp: '', durasi: 2, catatan: '' };
                        this.fetchActiveBookings();
                    }
                })
                .catch(err => {
                    const msg = err.response?.data?.message || 'Gagal booking';
                    Swal.fire({ icon: 'error', title: 'Gagal', text: msg, background: '#fff', color: '#374151' });
                })
                .finally(() => { this.bookingLoading = false; });
        },

        fetchActiveBookings() {
            axios.get('{{ route("admin.pos.booking-aktif") }}')
                .then(res => {
                    if (res.data.success) {
                        this.activeBookings = res.data.data;
                        this.startTimerPolling();
                    }
                })
                .catch(() => {});
        },

        startTimerPolling() {
            if (this.timerInterval) clearInterval(this.timerInterval);
            const ongoing = this.activeBookings.filter(b => b.status === 'ongoing');
            if (ongoing.length === 0) {
                this.timerData = {};
                return;
            }
            const poll = () => {
                ongoing.forEach(b => {
                    this.fetchTimerStatus(b.id);
                });
            };
            poll();
            this.timerInterval = setInterval(poll, 30000);
        },

        fetchTimerStatus(id) {
            axios.get('{{ url("admin/pos/booking") }}/' + id + '/timer')
                .then(res => {
                    if (res.data.success) {
                        const d = res.data.data;
                        const progress = d.progress || 0;
                        const total = d.total_detik || 1;
                        const sisa = d.sisa_detik || 0;
                        const berlalu = d.detik_berlalu || 0;
                        const display = this.formatTimer(sisa);
                        this.timerData = { ...this.timerData, [id]: { progress, display, is_expired: d.is_expired } };
                    }
                })
                .catch(() => {});
        },

        formatTimer(detik) {
            if (detik <= 0) return '00:00:00';
            const h = Math.floor(detik / 3600);
            const m = Math.floor((detik % 3600) / 60);
            const s = detik % 60;
            return [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
        },

        startSession(id) {
            if (this.bookingLoading) return;
            this.bookingLoading = true;
            axios.post('{{ url("admin/pos/booking") }}/' + id + '/start')
                .then(res => {
                    if (res.data.success) {
                        Swal.fire({ icon: 'success', title: 'Sesi Dimulai', text: 'Timer karaoke berjalan', timer: 2000, showConfirmButton: false, background: '#fff', color: '#374151', toast: true, position: 'top-end' });
                        this.fetchActiveBookings();
                    }
                })
                .catch(err => {
                    const msg = err.response?.data?.message || 'Gagal memulai sesi';
                    Swal.fire({ icon: 'error', title: 'Gagal', text: msg, background: '#fff', color: '#374151' });
                })
                .finally(() => { this.bookingLoading = false; });
        },

        openExtendModal(booking) {
            this.extendForm = { booking_id: booking.id, tambah_jam: 1 };
            this.showExtendModal = true;
        },

        extendSession() {
            if (this.bookingLoading) return;
            this.bookingLoading = true;
            axios.post('{{ url("admin/pos/booking") }}/' + this.extendForm.booking_id + '/extend', { tambah_jam: this.extendForm.tambah_jam })
                .then(res => {
                    if (res.data.success) {
                        Swal.fire({ icon: 'success', title: 'Diperpanjang', text: res.data.message || 'Sesi berhasil diperpanjang', timer: 2000, showConfirmButton: false, background: '#fff', color: '#374151', toast: true, position: 'top-end' });
                        this.showExtendModal = false;
                        this.fetchActiveBookings();
                    }
                })
                .catch(err => {
                    const msg = err.response?.data?.message || 'Gagal memperpanjang';
                    Swal.fire({ icon: 'error', title: 'Gagal', text: msg, background: '#fff', color: '#374151' });
                })
                .finally(() => { this.bookingLoading = false; });
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
