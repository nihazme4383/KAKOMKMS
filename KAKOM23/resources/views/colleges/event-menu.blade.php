@extends('layouts.app')

@section('content')
<div class="topbar">
    <div>
        <h1>{{ $selectedCollege->code }}</h1>
        <div class="muted">{{ $selectedCollege->name }} - Pilih acara untuk melihat borang kolej ini.</div>
    </div>
    <a class="button secondary" href="{{ route('dashboard') }}">Kembali</a>
</div>

<div class="panel">
    <h2>Acara</h2>
    <div class="grid">
        @foreach($events as $listedEvent)
            @php($registration = $registrations->get($listedEvent->id))
            <div class="stat">
                <span class="event-chip {{ $listedEvent->color_class }}">{{ $listedEvent->name }}</span>
                <strong>{{ $registration ? $registration->students->count() : 0 }}</strong>
                <div class="muted">Pelajar didaftarkan</div>
                <div class="actions" style="margin-top:12px;">
                    @if($registration)
                        <a class="button secondary" href="{{ route('registrations.show', $registration) }}">Lihat Borang</a>
                    @else
                        <span class="muted">Belum ada borang</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
