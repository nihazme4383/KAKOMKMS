@extends('layouts.app')

@section('content')
<div style="min-height:100vh;display:grid;place-items:center;padding:24px;">
    <div class="panel" style="width:100%;max-width:430px;">
        <h1>Pendaftaran Peserta KAKOM 23</h1>
        <p class="muted">Masukkan kod akses kolej atau urusetia.</p>

        @if($errors->any())
            <div class="errors">{{ $errors->first() }}</div>
        @endif

        <form method="post" action="{{ route('login.submit') }}">
            @csrf
            <label for="access_code">Kod akses</label>
            <input id="access_code" name="access_code" value="{{ old('access_code') }}" autofocus required>
            <button class="button" type="submit" style="width:100%;margin-top:14px;">Masuk</button>
        </form>
    </div>
</div>
@endsection
