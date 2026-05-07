@extends('layouts.app')

@section('content')
<div class="topbar">
    <div>
        <h1>{{ $registration->event->name }}</h1>
        <div class="muted">{{ $registration->college->name }} - Semakan pendaftaran</div>
    </div>
    <div class="actions">
        <a class="button secondary" href="{{ route('registrations.show.excel', $registration) }}">Eksport Excel</a>
        @if(! $college->isSecretariat())
            <a class="button" href="{{ route('registrations.edit', $registration->event) }}">Edit</a>
        @endif
    </div>
</div>

<div class="panel">
    <h2>Jurulatih dan Pengurus</h2>
    <table>
        <thead>
        <tr>
            <th>Peranan</th>
            <th>Nama</th>
            <th>No Kad Pengenalan</th>
            <th>Jawatan</th>
            <th>No Telefon</th>
        </tr>
        </thead>
        <tbody>
        @php($officialRoles = $registration->event->slug === 'bola-sepak'
            ? ['manager' => 'Pengurus', 'coach_1' => 'Jurulatih 1', 'coach_2' => 'Fisioterapi']
            : ['manager' => 'Pengurus', 'coach_1' => 'Jurulatih 1'])
        @foreach($officialRoles as $role => $label)
            @php($official = $officials->get($role))
            <tr>
                <td><strong>{{ $label }}</strong></td>
                <td>{{ optional($official)->name ?: '-' }}</td>
                <td>{{ optional($official)->ic_no ?: '-' }}</td>
                <td>{{ optional($official)->position ?: '-' }}</td>
                <td>{{ optional($official)->phone_no ?: '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="panel">
    <h2>Pelajar</h2>
    <table>
        <thead>
        <tr>
            <th style="width:52px;">Bil</th>
            <th>Nama</th>
            <th>No Matrik</th>
            <th>No Kad Pengenalan</th>
            @if($registration->event->usesHomeAwayJerseys())
                <th>No Jersi Home</th>
                <th>No Jersi Away</th>
            @elseif($registration->event->requires_jersey_no)
                <th>No Jersi</th>
            @endif
            <th>Dokumen</th>
        </tr>
        </thead>
        <tbody>
        @forelse($registration->students as $student)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $student->name }}</td>
                <td>{{ $student->matrix_no }}</td>
                <td>{{ $student->ic_no ?: '-' }}</td>
                @if($registration->event->usesHomeAwayJerseys())
                    <td>{{ $student->jersey_no ?: '-' }}</td>
                    <td>{{ $student->jersey_no_away ?: '-' }}</td>
                @elseif($registration->event->requires_jersey_no)
                    <td>{{ $student->jersey_no ?: '-' }}</td>
                @endif
                <td>
                    @if($student->identity_document_path)
                        <a href="{{ route('students.document', $student) }}">Lihat PDF</a>
                    @else
                        -
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ $registration->event->usesHomeAwayJerseys() ? 7 : ($registration->event->requires_jersey_no ? 6 : 5) }}" class="muted">Tiada pelajar direkodkan.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@if($registration->notes)
    <div class="panel">
        <h2>Catatan</h2>
        <p>{{ $registration->notes }}</p>
    </div>
@endif
@endsection
