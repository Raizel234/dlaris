@extends('layouts.guest')
@section('title', 'Verifikasi Email')

@section('hero_title')
    Verifikasi <span>Email</span>
@endsection

@section('hero_sub')
    Terima kasih sudah mendaftar! Silakan verifikasi alamat email Anda untuk mengaktifkan akun.
@endsection

@section('content')
<div class="form-card-header">
    <div style="width:56px;height:56px;background:linear-gradient(135deg, var(--green-500), var(--green-700));border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
        <i class="fa-solid fa-envelope-circle-check" style="color:#fff;font-size:1.3rem;"></i>
    </div>
    <h2>Verifikasi Email</h2>
    <p>Periksa kotak masuk email Anda</p>
</div>

<p style="font-size:0.88rem;color:var(--text-muted);text-align:center;line-height:1.6;margin-bottom:1.25rem;">
    Sebelum melanjutkan, periksa email Anda untuk tautan verifikasi.
    Jika tidak menerima email, klik tombol di bawah untuk mengirim ulang.
</p>

@if (session('status') == 'verification-link-sent')
<div class="alert-box alert-success">
    <i class="fa-solid fa-circle-check"></i>
    Tautan verifikasi baru telah dikirim ke email Anda.
</div>
@endif

<div style="display:flex;flex-direction:column;gap:0.75rem;">
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn-submit">
            <i class="fa-solid fa-paper-plane"></i>
            Kirim Ulang Verifikasi
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
        </button>
    </form>
</div>
@endsection