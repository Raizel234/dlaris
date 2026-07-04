@extends('admin.layouts.app')
@section('title', 'Kalender Booking')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet">
<style>
    .fc-toolbar-title { font-size: 1.25rem !important; font-weight: 600; }
    .fc-button-primary { background-color: #059669 !important; border-color: #059669 !important; }
    .fc-button-primary:hover { background-color: #047857 !important; border-color: #047857 !important; }
    .fc-button-primary:not(:disabled).fc-button-active { background-color: #047857 !important; border-color: #047857 !important; }
    .fc-daygrid-event { border-radius: 6px !important; padding: 2px 4px !important; font-size: .8rem !important; }
    .fc-event-title { font-weight: 500 !important; }
    .booking-detail-card { border-left: 4px solid; border-radius: 8px; }
    .status-pending { border-left-color: #f59e0b; }
    .status-confirmed { border-left-color: #3b82f6; }
    .status-ongoing { border-left-color: #10b981; }
    .badge-pending { background-color: #fef3c7; color: #92400e; }
    .badge-confirmed { background-color: #dbeafe; color: #1e40af; }
    .badge-ongoing { background-color: #d1fae5; color: #065f46; }
</style>
@endpush

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
        <h2 class="fw-bold mb-1">Kalender Booking</h2>
        <p class="text-muted mb-0">Lihat jadwal booking ruangan karaoke</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.booking.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-table me-1"></i>Tabel
        </a>
        <a href="{{ route('admin.ruangan.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-building me-1"></i>Ruangan
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom fw-semibold">
                <i class="bi bi-funnel me-1"></i>Filter
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Ruangan</label>
                    <select id="filterRuangan" class="form-select form-select-sm">
                        <option value="">Semua Ruangan</option>
                        @foreach($ruangans as $r)
                        <option value="{{ $r->id }}">{{ $r->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Legenda</label>
                    <div class="d-flex flex-column gap-1 small">
                        <span class="d-flex align-items-center gap-2"><span class="badge" style="background:#f59e0b;">&nbsp;&nbsp;&nbsp;</span> Pending</span>
                        <span class="d-flex align-items-center gap-2"><span class="badge" style="background:#3b82f6;">&nbsp;&nbsp;&nbsp;</span> Confirmed</span>
                        <span class="d-flex align-items-center gap-2"><span class="badge" style="background:#10b981;">&nbsp;&nbsp;&nbsp;</span> Ongoing</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white border-bottom fw-semibold">
                <i class="bi bi-info-circle me-1"></i>Detail Booking
            </div>
            <div class="card-body" id="bookingDetail">
                <p class="text-muted small mb-0">Klik event pada kalender untuk melihat detail</p>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-calendar-check text-success me-2"></i>Detail Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="bookingModalBody">
                <div class="text-center py-3">
                    <div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="btnLihatDetail" class="btn btn-success">Lihat Detail</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const filterRuangan = document.getElementById('filterRuangan');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        locale: 'id',
        height: 'auto',
        firstDay: 1,
        slotMinTime: '08:00:00',
        slotMaxTime: '23:00:00',
        eventClick: function(info) {
            const props = info.event.extendedProps;
            const statusColors = {
                pending: { badge: 'badge-pending', label: 'Pending' },
                confirmed: { badge: 'badge-confirmed', label: 'Confirmed' },
                ongoing: { badge: 'badge-ongoing', label: 'Ongoing' },
            };
            const sc = statusColors[props.status] || { badge: 'bg-secondary', label: props.status };

            document.getElementById('bookingModalBody').innerHTML = `
                <div class="booking-detail-card p-3 status-${props.status} bg-light rounded mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <strong class="fs-6">${props.ruangan}</strong>
                        <span class="badge ${sc.badge}">${sc.label}</span>
                    </div>
                    <div class="small">
                        <div class="mb-1"><i class="bi bi-person me-1"></i><strong>Pemesan:</strong> ${props.pemesan}</div>
                        <div class="mb-1"><i class="bi bi-calendar me-1"></i><strong>Tanggal:</strong> ${props.tanggal}</div>
                        <div class="mb-1"><i class="bi bi-clock me-1"></i><strong>Jam:</strong> ${props.jam_mulai} - ${props.jam_selesai}</div>
                        <div class="mb-1"><i class="bi bi-hourglass me-1"></i><strong>Durasi:</strong> ${props.durasi} jam</div>
                        <div class="mb-0"><i class="bi bi-cash me-1"></i><strong>Total:</strong> <span class="text-success fw-bold">Rp ${new Intl.NumberFormat('id-ID').format(props.total_harga)}</span></div>
                    </div>
                </div>
            `;
            document.getElementById('btnLihatDetail').href = '{{ url('admin/booking') }}/' + info.event.id;

            const modal = new bootstrap.Modal(document.getElementById('bookingModal'));
            modal.show();
        },
        eventDidMount: function(info) {
            const props = info.event.extendedProps;
            const tooltip = `${props.ruangan} - ${props.pemesan}\n${props.tanggal} ${props.jam_mulai}-${props.jam_selesai}`;
            info.el.title = tooltip;
            info.el.style.cursor = 'pointer';
        },
        loading: function(isLoading) {
            if (isLoading) {
                document.getElementById('bookingDetail').innerHTML = '<p class="text-muted small mb-0"><i class="bi bi-arrow-repeat me-1"></i>Memuat...</p>';
            }
        },
    });

    calendar.render();

    function loadEvents() {
        const ruanganId = filterRuangan.value;
        const params = new URLSearchParams();
        if (ruanganId) params.append('ruangan_id', ruanganId);

        fetch(`{{ route('admin.booking.calendar-data') }}?${params.toString()}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(events => {
            calendar.removeAllEvents();
            calendar.addEventSource(events);
        });
    }

    filterRuangan.addEventListener('change', loadEvents);
    loadEvents();
});
</script>
@endpush