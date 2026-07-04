@extends('layouts.pelanggan')
@section('title', 'Booking Karaoke')

@section('content')
<div class="container px-3 px-md-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="fw-bold mb-1">Booking Karaoke</h2>
                    <p class="text-muted mb-0">Pesan ruangan karaoke untuk acara Anda</p>
                </div>
                <a href="{{ route('pelanggan.booking.daftar') }}" class="btn btn-outline-primary rounded-pill btn-sm">
                    <i class="bi bi-list me-1"></i>Booking Saya
                </a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form id="formBooking" class="needs-validation" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Ruangan</label>
                            <select name="ruangan_id" id="ruangan_id" class="form-select" required>
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach($ruangans as $r)
                                <option value="{{ $r->id }}" data-harga="{{ $r->tarif_per_jam }}">
                                    {{ $r->nama }} ({{ $r->kapasitas }} org) - Rp {{ number_format($r->tarif_per_jam, 0, ',', '.') }}/jam
                                </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Pilih ruangan terlebih dahulu</div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal</label>
                                <input type="date" name="tanggal" id="tanggal" min="{{ date('Y-m-d') }}"
                                       class="form-control" required>
                                <div class="invalid-feedback">Pilih tanggal booking</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jam Mulai</label>
                                <input type="time" name="jam_mulai" id="jam_mulai"
                                       class="form-control" required>
                                <div class="invalid-feedback">Pilih jam mulai</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Durasi</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="input-group" style="width:140px;">
                                    <button type="button" class="btn btn-outline-secondary" onclick="adjustDurasi(-1)">-</button>
                                    <input type="number" name="durasi" id="durasi" min="1" max="12" value="2"
                                           class="form-control text-center" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="adjustDurasi(1)">+</button>
                                </div>
                                <span class="text-muted">Jam</span>
                                <span id="totalHarga" class="ms-auto fw-bold text-success fs-5">Rp 0</span>
                            </div>
                        </div>

                        <div id="ketersediaanInfo" class="alert d-none"></div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Catatan <span class="text-muted">(opsional)</span></label>
                            <textarea name="catatan" rows="2" class="form-control" placeholder="Catatan tambahan..."></textarea>
                        </div>

                        <button type="submit" id="btnBooking"
                                class="btn btn-success w-100 py-2 fw-bold">
                            <i class="bi bi-calendar-check me-2"></i>Booking Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ruanganSelect = document.getElementById('ruangan_id');
    const tanggalInput = document.getElementById('tanggal');
    const jamMulai = document.getElementById('jam_mulai');
    const durasiInput = document.getElementById('durasi');
    const totalHarga = document.getElementById('totalHarga');
    const ketersediaanInfo = document.getElementById('ketersediaanInfo');
    const btnBooking = document.getElementById('btnBooking');

    if (!tanggalInput.value) {
        const today = new Date().toISOString().split('T')[0];
        tanggalInput.value = today;
    }
    if (!jamMulai.value) {
        const now = new Date();
        const h = String(now.getHours()).padStart(2,'0');
        const m = String(Math.ceil(now.getMinutes()/30)*30 % 60).padStart(2,'0');
        jamMulai.value = h + ':' + (m || '00');
    }

    function adjustDurasi(delta) {
        const val = parseInt(durasiInput.value) || 1;
        const newVal = Math.min(12, Math.max(1, val + delta));
        durasiInput.value = newVal;
        hitungHarga();
        cekKetersediaan();
    }
    window.adjustDurasi = adjustDurasi;

    function hitungHarga() {
        const selected = ruanganSelect.options[ruanganSelect.selectedIndex];
        const harga = selected ? parseFloat(selected.dataset.harga || 0) : 0;
        const durasi = parseInt(durasiInput.value) || 0;
        totalHarga.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(harga * durasi);
    }

    function cekKetersediaan() {
        const ruanganId = ruanganSelect.value;
        const tanggal = tanggalInput.value;
        const jam = jamMulai.value;
        const durasi = durasiInput.value;

        if (!ruanganId || !tanggal || !jam || !durasi) {
            ketersediaanInfo.classList.add('d-none');
            return;
        }

        btnBooking.disabled = true;
        btnBooking.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memeriksa...';

        fetch('{{ route("pelanggan.booking.cek") }}?ruangan_id=' + ruanganId + '&tanggal=' + tanggal + '&jam_mulai=' + jam + '&durasi=' + durasi, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                ketersediaanInfo.classList.remove('d-none');
                if (d.data.tersedia) {
                    ketersediaanInfo.className = 'alert alert-success';
                    ketersediaanInfo.innerHTML = '<i class="bi bi-check-circle me-1"></i> Ruangan tersedia';
                    btnBooking.disabled = false;
                    btnBooking.innerHTML = '<i class="bi bi-calendar-check me-2"></i>Booking Sekarang';
                } else {
                    ketersediaanInfo.className = 'alert alert-danger';
                    ketersediaanInfo.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i> Ruangan tidak tersedia pada waktu tersebut';
                }
            }
        })
        .catch(() => {
            ketersediaanInfo.classList.remove('d-none');
            ketersediaanInfo.className = 'alert alert-warning';
            ketersediaanInfo.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> Gagal cek ketersediaan';
        })
        .finally(() => {
            if (!btnBooking.disabled) {
                btnBooking.innerHTML = '<i class="bi bi-calendar-check me-2"></i>Booking Sekarang';
            }
        });
    }

    ruanganSelect.addEventListener('change', () => { hitungHarga(); cekKetersediaan(); });
    tanggalInput.addEventListener('change', cekKetersediaan);
    jamMulai.addEventListener('change', cekKetersediaan);
    durasiInput.addEventListener('input', () => { hitungHarga(); cekKetersediaan(); });

    document.getElementById('formBooking').addEventListener('submit', function(e) {
        e.preventDefault();
        if (!this.checkValidity()) { this.classList.add('was-validated'); return; }

        btnBooking.disabled = true;
        btnBooking.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...';

        const data = {
            ruangan_id: ruanganSelect.value,
            tanggal: tanggalInput.value,
            jam_mulai: jamMulai.value,
            durasi: durasiInput.value,
            catatan: document.querySelector('[name=catatan]').value,
        };

        fetch('{{ route("pelanggan.booking.store") }}', {
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
                    title: 'Booking Berhasil!',
                    text: 'Booking ruangan ' + d.data.ruangan.nama + ' menunggu konfirmasi',
                    confirmButtonColor: '#059669',
                    confirmButtonText: 'OK',
                }).then(() => {
                    window.location.href = '{{ route("pelanggan.booking.daftar") }}';
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: d.message, confirmButtonColor: '#059669' });
                btnBooking.disabled = false;
                btnBooking.innerHTML = '<i class="bi bi-calendar-check me-2"></i>Booking Sekarang';
            }
        })
        .catch(() => {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan server', confirmButtonColor: '#059669' });
            btnBooking.disabled = false;
            btnBooking.innerHTML = '<i class="bi bi-calendar-check me-2"></i>Booking Sekarang';
        });
    });

    hitungHarga();
});
</script>
@endsection