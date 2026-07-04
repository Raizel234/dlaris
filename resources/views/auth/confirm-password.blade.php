@extends('layouts.guest')
@section('title', 'Konfirmasi Password')

@section('hero_title')
    Konfirmasi <span>Password</span>
@endsection

@section('hero_sub')
    Masukkan password Anda untuk melanjutkan ke area aman.
@endsection

@section('content')
<div class="form-card-header">
    <div class="fch-icon"><i class="fa-solid fa-shield-check"></i></div>
    <h2>Konfirmasi Password</h2>
    <p>Verifikasi identitas Anda untuk melanjutkan</p>
</div>

@if ($errors->any())
<div class="alert-box alert-error">
    <i class="fa-solid fa-circle-exclamation"></i>
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('password.confirm') }}">
    @csrf

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

    <button type="submit" class="btn-submit">
        <i class="fa-solid fa-check"></i>
        Konfirmasi
    </button>
</form>
@endsection