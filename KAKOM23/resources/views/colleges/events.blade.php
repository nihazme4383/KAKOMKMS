@extends('layouts.app')

@section('content')
<div class="topbar">
    <div>
        <h1>Acara Kolej</h1>
        <div class="muted">{{ $selectedCollege->name }} - Senarai acara yang disahkan.</div>
    </div>
    <div class="actions">
        <a class="button" href="{{ route('colleges.events.pdf', $selectedCollege) }}">Eksport PDF</a>
        <a class="button secondary" href="{{ route('colleges.events.excel', $selectedCollege) }}">Eksport Excel</a>
        <a class="button secondary" href="{{ route('dashboard') }}">Kembali</a>
    </div>
</div>

<div class="panel">
    <h2>Acara Terlibat</h2>
    <table>
        <thead>
        <tr>
            <th style="width:70px;">Bil</th>
            <th>Acara</th>
            <th style="width:140px;">Had Pelajar</th>
            <th style="width:150px;">Pelajar Daftar</th>
            <th style="width:180px;">Status Borang</th>
            <th style="width:120px;">Tindakan</th>
        </tr>
        </thead>
        <tbody>
        @forelse($confirmedEvents as $confirmation)
            @php($event = $confirmation->event)
            @php($registration = $event ? $registrations->get($event->id) : null)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    @if($event)
                        <span class="event-chip {{ $event->color_class }}">{{ $event->name }}</span>
                    @else
                        -
                    @endif
                </td>
                <td>{{ $event ? $event->max_students . ' orang' : '-' }}</td>
                <td>{{ $registration ? $registration->students->count() : 0 }}</td>
                <td>{{ $registration ? 'Borang telah dibuat' : 'Belum ada borang' }}</td>
                <td>
                    @if($registration)
                        <a class="button secondary" href="{{ route('registrations.show', $registration) }}">Lihat</a>
                    @else
                        -
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="muted">Kolej ini belum mengesahkan penyertaan acara.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
