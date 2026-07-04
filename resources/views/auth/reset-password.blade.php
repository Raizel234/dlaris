@extends('layouts.guest')
@section('title', 'Reset Password')

@section('hero_title')
    Buat Password <span>Baru</span>
@endsection

@section('hero_sub')
    Masukkan password baru untuk akun Anda. Pastikan minimal 8 karakter dan kuat.
@endsection

@section('content')
<div class="form-card-header">
    <div class="fch-icon"><i class="fa-solid fa-shield-keyhole"></i></div>
    <h2>Reset Password</h2>
    <p>Buat password baru yang aman</p>
</div>

<form method="POST" action="{{ route('password.store') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div class="form-group">
        <label for="email">Alamat Email</label>
        <div class="form-input-wrap">
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}"
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
        <label for="password">Password Baru</label>
        <div class="form-input-wrap">
            <input id="password" type="password" name="password"
                   required autocomplete="new-password"
                   class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                   placeholder="Minimal 8 karakter">
            <i class="fa-solid fa-lock input-icon"></i>
            <button type="button" class="toggle-password">
                <i class="fa-regular fa-eye"></i>
            </button>
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
        <i class="fa-solid fa-arrow-rotate-left"></i>
        Reset Password
    </button>
</form>

<div class="form-footer">
    <a href="{{ route('login') }}">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Login
    </a>
</div>
@endsection