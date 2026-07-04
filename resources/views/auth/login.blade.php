@extends('layouts.guest')
@section('title', 'Masuk')

@section('hero_title')
    Selamat Datang <span>Kembali</span>
@endsection

@section('hero_sub')
    Masuk ke akun Anda untuk memesan menu favorit, booking ruangan karaoke, dan menikmati promo spesial D'LARIS.
@endsection

@section('content')
<div class="form-card-header">
    <div class="fch-icon"><i class="fa-solid fa-right-to-bracket"></i></div>
    <h2>Masuk ke Akun</h2>
    <p>Masukkan kredensial Anda untuk melanjutkan</p>
</div>

@if (session('status'))
<div class="alert-box alert-success">
    <i class="fa-solid fa-circle-check"></i>
    {{ session('status') }}
</div>
@endif

@if ($errors->any())
<div class="alert-box alert-error">
    <i class="fa-solid fa-circle-exclamation"></i>
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="form-group">
        <label for="email">Alamat Email</label>
        <div class="form-input-wrap">
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   required autofocus autocomplete="username"
                   class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                   placeholder="contoh@email.com">
            <i class="fa-regular fa-envelope input-icon"></i>
        </div>
        @error('email')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <div class="form-input-wrap">
            <input id="password" type="password" name="password"
                   required autocomplete="current-password"
                   class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                   placeholder="Masukkan password Anda">
            <i class="fa-solid fa-lock input-icon"></i>
            <button type="button" class="toggle-password">
                <i class="fa-regular fa-eye"></i>
            </button>
        </div>
        @error('password')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    <div class="remember-row">
        <div class="form-check-custom">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember">Ingat saya</label>
        </div>
        @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
        @endif
    </div>

    <button type="submit" class="btn-submit">
        <i class="fa-solid fa-right-to-bracket"></i>
        Masuk Sekarang
    </button>
</form>

<div class="form-divider">atau</div>

<a href="{{ route('auth.google') }}" class="btn-google">
    <i class="fa-brands fa-google"></i>
    Lanjutkan dengan Google
</a>

<div class="form-footer">
    <p>Belum punya akun? <a href="{{ route('register') }}">Daftar Sekarang</a></p>
</div>
@endsection