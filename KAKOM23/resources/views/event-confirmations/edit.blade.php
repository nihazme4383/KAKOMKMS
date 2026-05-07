@extends('layouts.app')

@section('content')
<div class="topbar">
    <div>
        <h1>Pengesahan Acara</h1>
        <div class="muted">{{ $college->name }} - Tandakan acara yang akan disertai.</div>
    </div>
</div>

@if(session('status'))
    <div class="alert">{{ session('status') }}</div>
@endif

@if($errors->any())
    <div class="errors">
        Sila semak semula pilihan acara.
    </div>
@endif

<form method="post" action="{{ route('event-confirmations.update') }}">
    @csrf

    <div class="panel">
        <h2>Senarai Acara</h2>
        <table>
            <thead>
            <tr>
                <th style="width:70px;">Bil</th>
                <th>Acara</th>
                <th style="width:170px;">Had Pelajar</th>
                <th style="width:160px;">Penyertaan</th>
            </tr>
            </thead>
            <tbody>
            @foreach($events as $event)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><span class="event-chip {{ $event->color_class }}">{{ $event->name }}</span></td>
                    <td>{{ $event->max_students }} orang</td>
                    <td>
                        <label class="checkbox-field">
                            <input
                                type="checkbox"
                                name="events[]"
                                value="{{ $event->id }}"
                                {{ in_array($event->id, old('events', $confirmedEventIds)) ? 'checked' : '' }}
                            >
                            Ambil bahagian
                        </label>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="actions">
        <button class="button" type="submit">Simpan Pengesahan</button>
        <a class="button secondary" href="{{ route('dashboard') }}">Kembali</a>
    </div>
</form>
@endsection
