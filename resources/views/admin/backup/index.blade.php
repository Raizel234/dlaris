@extends('admin.layouts.app')
@section('title', 'Backup Database')

@push('styles')
<style>
    @media (max-width: 768px) {
        .table-responsive-custom { display: block; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .table-responsive-custom table { min-width: 500px; }
    }
</style>
@endpush

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Backup Database</h2>
        <p class="text-gray-600">Kelola backup database aplikasi</p>
    </div>
    <form action="{{ route('admin.backup.create') }}" method="POST">
        @csrf
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
            <i class="fa-solid fa-database"></i> Buat Backup Baru
        </button>
    </form>
</div>

{{-- Warning --}}
<div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4 mb-6">
    <div class="flex items-start gap-3">
        <i class="fa-solid fa-triangle-exclamation text-yellow-500 mt-0.5"></i>
        <div>
            <h4 class="text-sm font-semibold text-yellow-800">Perhatian!</h4>
            <p class="text-sm text-yellow-700 mt-1">
                Backup database berisi seluruh data aplikasi termasuk data transaksi, menu, karyawan, dan pengaturan.
                Disarankan untuk melakukan backup secara berkala. File backup disimpan di server dan dapat diunduh kapan saja.
            </p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm">
    <div class="p-4 border-b flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <span>Total: <strong>{{ $backups->count() }}</strong> file backup</span>
        </div>
    </div>

    <div class="overflow-x-auto table-responsive-custom">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 text-left text-sm font-semibold text-gray-600">
                    <th class="px-6 py-3 w-16">No</th>
                    <th class="px-6 py-3">Nama File</th>
                    <th class="px-6 py-3">Ukuran</th>
                    <th class="px-6 py-3">Tanggal</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($backups as $i => $backup)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ $i + 1 }}</td>
                    <td class="px-6 py-4 font-medium text-gray-800">
                        <i class="fa-solid fa-file-archive text-gray-400 mr-2"></i>
                        {{ $backup['filename'] }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $size = $backup['size'];
                            if ($size >= 1073741824) {
                                $formatted = number_format($size / 1073741824, 2) . ' GB';
                            } elseif ($size >= 1048576) {
                                $formatted = number_format($size / 1048576, 2) . ' MB';
                            } elseif ($size >= 1024) {
                                $formatted = number_format($size / 1024, 2) . ' KB';
                            } else {
                                $formatted = $size . ' B';
                            }
                        @endphp
                        {{ $formatted }}
                    </td>
                    <td class="px-6 py-4 text-gray-500">{{ date('d/m/Y H:i', $backup['last_modified']) }}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.backup.download', $backup['filename']) }}" class="text-blue-600 hover:text-blue-800 mx-1" title="Download">
                            <i class="fa-solid fa-download"></i>
                        </a>
                        <button onclick="hapusBackup('{{ $backup['filename'] }}')" class="text-red-600 hover:text-red-800 mx-1" title="Hapus">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        <i class="fa-solid fa-database text-3xl mb-2 block text-gray-300"></i>
                        Belum ada file backup. Klik "Buat Backup Baru" untuk memulai.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
function hapusBackup(filename) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus file backup "' + filename + '"?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('admin/backup') }}/${filename}`;
            form.innerHTML = `@csrf @method('DELETE')`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
