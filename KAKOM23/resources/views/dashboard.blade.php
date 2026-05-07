@extends('layouts.app')

@section('content')
<div class="dashboard-page">
<div class="topbar">
    <div>
        <h1>Dashboard</h1>
        <div class="muted">Ringkasan pendaftaran peserta mengikut acara.</div>
    </div>
    <div class="actions">
        <a class="button" href="{{ route('dashboard.pdf') }}">Eksport PDF</a>
        <a class="button secondary" href="{{ route('dashboard.excel') }}">Eksport Excel</a>
    </div>
</div>

<div class="grid">
    <div class="stat stat-events">
        Acara
        <strong>{{ $events->count() }}</strong>
    </div>
    <div class="stat stat-forms">
        Borang dihantar
        <strong>{{ $registrations->count() }}</strong>
    </div>
    <div class="stat stat-students">
        Jumlah pelajar
        <strong>{{ $registrations->sum(fn($registration) => $registration->students->count()) }}</strong>
    </div>
</div>

<div class="panel notice-panel" style="margin-top:18px;">
    <h2>Pengumuman Tarikh-Tarikh Penting</h2>
    <table class="notice-table">
        <tbody>
        <tr>
            <td><strong>Tarikh Tutup Pengesahan Acara</strong></td>
            <td>20 Jun 2026</td>
            <td>
                @if(! $college->isSecretariat())
                    <a class="button secondary" href="{{ route('event-confirmations.edit') }}">Klik</a>
                @else
                    <a class="button secondary" href="{{ route('event-confirmations.edit') }}">Semak</a>
                @endif
            </td>
        </tr>
        <tr>
            <td><strong>Tarikh Pengundian</strong></td>
            <td>14 Julai 2026</td>
            <td>-</td>
        </tr>
        <tr>
            <td><strong>Tarikh Hantar Softcopy</strong></td>
            <td>8 Ogos 2026</td>
            <td>-</td>
        </tr>
        <tr>
            <td><strong>Tarikh Hantar Borang Hardcopy</strong></td>
            <td>20 Ogos 2026</td>
            <td>-</td>
        </tr>
        <tr>
            <td><strong>KAKOM</strong></td>
            <td>20 - 24 Ogos 2026</td>
            <td>-</td>
        </tr>
        </tbody>
    </table>
</div>

<div class="panel registration-panel" style="margin-top:18px;">
    <h2>{{ $college->isSecretariat() ? 'Senarai Kolej' : 'Pendaftaran Kolej Saya' }}</h2>
    <table class="dashboard-table">
        <thead>
        @if($college->isSecretariat())
        <tr>
            <th>Kolej</th>
            <th>Acara Disahkan</th>
            <th>Borang Dihantar</th>
            <th>Tindakan</th>
        </tr>
        @else
        <tr>
            <th>Kolej</th>
            <th>Acara</th>
            <th>Pelajar</th>
            <th>Kemaskini</th>
            <th>Tindakan</th>
        </tr>
        @endif
        </thead>
        <tbody>
        @if($college->isSecretariat())
            @forelse($colleges as $listedCollege)
                <tr>
                    <td><strong>{{ $listedCollege->name }}</strong></td>
                    <td>{{ $listedCollege->event_confirmations_count }}</td>
                    <td>{{ $listedCollege->registrations_count }}</td>
                    <td>
                        <a class="button secondary" href="{{ route('colleges.events', $listedCollege) }}">Lihat Acara</a>
                        <a class="button secondary" href="{{ route('colleges.events.names.excel', $listedCollege) }}">Eksport Excel</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="muted">Tiada kolej direkodkan.</td>
                </tr>
            @endforelse
        @else
        @forelse($registrations as $registration)
            <tr>
                <td>{{ $registration->college->name }}</td>
                <td><span class="event-chip {{ $registration->event->color_class }}">{{ $registration->event->name }}</span></td>
                <td>{{ $registration->students->count() }}</td>
                <td>{{ $registration->updated_at->format('d/m/Y h:i A') }}</td>
                <td>
                    <a class="button secondary" href="{{ route('registrations.show', $registration) }}">Lihat</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="muted">Tiada pendaftaran lagi.</td>
            </tr>
        @endforelse
        @endif
        </tbody>
    </table>
</div>
</div>
@endsection
