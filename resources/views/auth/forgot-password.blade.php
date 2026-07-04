@extends('layouts.guest')
@section('title', 'Lupa Password')

@section('hero_title')
    Lupa <span>Password?</span>
@endsection

@section('hero_sub')
    Tenang, masukkan email Anda dan kami akan kirimkan tautan untuk mereset password Anda.
@endsection

@section('content')
<div class="form-card-header">
    <div class="fch-icon"><i class="fa-solid fa-key"></i></div>
    <h2>Reset Password</h2>
    <p>Kami akan kirim link reset ke email Anda</p>
</div>

@if (session('status'))
<div class="alert-box alert-success">
    <i class="fa-solid fa-circle-check"></i>
    {{ session('status') }}
</div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="form-group">
        <label for="email">Alamat Email</label>
        <div class="form-input-wrap">
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   required autofocus
                   class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                   placeholder="contoh@email.com">
            <i class="fa-regular fa-envelope input-icon"></i>
        </div>
        @error('email')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    <button type="submit" class="btn-submit">
        <i class="fa-solid fa-paper-plane"></i>
        Kirim Link Reset
    </button>
</form>

<div class="form-footer">
    <a href="{{ route('login') }}">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Login
    </a>
</div>
@endsection