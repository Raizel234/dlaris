@extends('layouts.guest')
@section('title', 'Daftar Akun')

@section('hero_title')
    Buat Akun <span>Baru</span>
@endsection

@section('hero_sub')
    Daftar sekarang dan nikmati kemudahan memesan menu favorit, booking ruangan karaoke, serta berbagai promo spesial dari D'LARIS.
@endsection

@section('content')
<div class="form-card-header">
    <div class="fch-icon"><i class="fa-solid fa-user-plus"></i></div>
    <h2>Buat Akun Baru</h2>
    <p>Isi data diri Anda untuk mendaftar</p>
</div>

@if ($errors->any())
<div class="alert-box alert-error">
    <i class="fa-solid fa-circle-exclamation"></i>
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="form-group">
        <label for="name">Nama Lengkap</label>
        <div class="form-input-wrap">
            <input id="name" type="text" name="name" value="{{ old('name') }}"
                   required autofocus autocomplete="name"
                   class="{{ $errors->has('name') ? 'is-invalid' : '' }}"
                   placeholder="Nama lengkap Anda">
            <i class="fa-regular fa-user input-icon"></i>
        </div>
        @error('name')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="email">Alamat Email</label>
        <div class="form-input-wrap">
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   required autocomplete="username"
                   class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                   placeholder="contoh@email.com">
            <i class="fa-regular fa-envelope input-icon"></i>
        </div>
        @error('email')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="nomor_hp">Nomor HP <span style="color:var(--text-muted);font-weight:400;">(opsional)</span></label>
        <div class="form-input-wrap">
            <input id="nomor_hp" type="text" name="nomor_hp" value="{{ old('nomor_hp') }}"
                   placeholder="08xxxxxxxxxx">
            <i class="fa-solid fa-phone input-icon"></i>
        </div>
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <div class="form-input-wrap">
            <input id="password" type="password" name="password"
                   required autocomplete="new-password" oninput="checkStrength(this)"
                   class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                   placeholder="Minimal 8 karakter">
            <i class="fa-solid fa-lock input-icon"></i>
            <button type="button" class="toggle-password">
                <i class="fa-regular fa-eye"></i>
            </button>
        </div>
        <div class="password-strength" id="passwordStrength">
            <div class="ps-bar"><div class="ps-fill" id="psFill"></div></div>
            <span class="ps-text" id="psText"></span>
        </div>
        @error('password')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="password_confirmation">Konfirmasi Password</label>
        <div class="form-input-wrap">
            <input id="password_confirmation" type="password" name="password_confirmation"
                   required autocomplete="new-password"
                   placeholder="Ulangi password">
            <i class="fa-solid fa-lock input-icon"></i>
            <button type="button" class="toggle-password">
                <i class="fa-regular fa-eye"></i>
            </button>
        </div>
    </div>

    <button type="submit" class="btn-submit">
        <i class="fa-solid fa-user-plus"></i>
        Daftar Sekarang
    </button>
</form>

<div class="form-divider">atau</div>

<div class="form-footer">
    <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
</div>
@endsection

@push('scripts')
<script>
function checkStrength(input) {
    const bar = document.getElementById('psFill');
    const text = document.getElementById('psText');
    if (!input.value) { bar.style.width='0'; bar.style.background=''; text.textContent=''; return; }
    let score = 0;
    if (input.value.length >= 8) score++;
    if (/[a-z]/.test(input.value) && /[A-Z]/.test(input.value)) score++;
    if (/\d/.test(input.value)) score++;
    if (/[^a-zA-Z0-9]/.test(input.value)) score++;
    const levels = ['','Lemah','Cukup','Kuat','Sangat Kuat'];
    const colors = ['','#ef4444','#f59e0b','#22c55e','#16a34a'];
    bar.style.width = (score * 25) + '%';
    bar.style.background = colors[score];
    text.textContent = levels[score];
    text.style.color = colors[score];
}
</script>
@endpush