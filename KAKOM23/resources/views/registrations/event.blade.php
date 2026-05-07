@extends('layouts.app')

@section('content')
<div class="topbar">
    <div>
        <h1>{{ $event->name }}</h1>
        <div class="muted">Senarai pendaftaran untuk urusetia.</div>
    </div>
    <div class="actions">
        <a class="button secondary" href="{{ route('registrations.excel', $event) }}">Eksport Excel</a>
    </div>
</div>

<div class="panel">
    <table>
        <thead>
        <tr>
            <th>Kolej</th>
            <th>Pelajar</th>
            <th>Kemaskini</th>
            <th>Tindakan</th>
        </tr>
        </thead>
        <tbody>
        @forelse($registrations as $registration)
            <tr>
                <td>{{ $registration->college->name }}</td>
                <td>{{ $registration->students->count() }}</td>
                <td>{{ $registration->updated_at->format('d/m/Y h:i A') }}</td>
                <td><a class="button secondary" href="{{ route('registrations.show', $registration) }}">Lihat</a></td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="muted">Belum ada pendaftaran untuk acara ini.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
