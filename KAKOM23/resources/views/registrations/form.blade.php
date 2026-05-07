@extends('layouts.app')

@section('content')
<div class="topbar">
    <div>
        <h1>{{ $event->name }}</h1>
        <div class="muted">{{ $college->name }} - Borang pendaftaran peserta</div>
    </div>
    <div class="actions">
        <a class="button" href="{{ route('registrations.pdf', $event) }}">Eksport PDF</a>
        <a class="button secondary" href="{{ route('registrations.excel', $event) }}">Eksport Excel</a>
    </div>
</div>

@if(session('status'))
    <div class="alert">{{ session('status') }}</div>
@endif

@if($errors->any())
    <div class="errors">
        Sila semak semula maklumat yang dimasukkan.
    </div>
@endif

<form method="post" action="{{ route('registrations.update', $event) }}" enctype="multipart/form-data">
    @csrf

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
            @php($officialRoles = $event->slug === 'bola-sepak'
                ? ['manager' => 'Pengurus', 'coach_1' => 'Jurulatih 1', 'coach_2' => 'Fisioterapi']
                : ['manager' => 'Pengurus', 'coach_1' => 'Jurulatih 1'])
            @foreach($officialRoles as $role => $label)
                @php($official = $officials->get($role))
                <tr>
                    <td><strong>{{ $label }}</strong></td>
                    <td><input name="officials[{{ $role }}][name]" value="{{ old("officials.$role.name", optional($official)->name) }}"></td>
                    <td><input name="officials[{{ $role }}][ic_no]" value="{{ old("officials.$role.ic_no", optional($official)->ic_no) }}"></td>
                    <td><input name="officials[{{ $role }}][position]" value="{{ old("officials.$role.position", optional($official)->position) }}"></td>
                    <td><input name="officials[{{ $role }}][phone_no]" value="{{ old("officials.$role.phone_no", optional($official)->phone_no) }}"></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="panel">
        <h2>Pelajar</h2>
        <p class="muted">Had pelajar untuk acara ini: {{ $event->max_students }} orang.</p>
        <table>
            <thead>
            <tr>
                <th style="width:52px;">Bil</th>
                <th>Nama</th>
                <th>No Matrik</th>
                <th>No Kad Pengenalan</th>
                @if($event->usesHomeAwayJerseys())
                    <th style="width:130px;">No Jersi Home</th>
                    <th style="width:130px;">No Jersi Away</th>
                @elseif($event->requires_jersey_no)
                    <th style="width:150px;">No Jersi</th>
                @endif
                <th>PDF Kad Matrik/Kad Pengenalan</th>
            </tr>
            </thead>
            <tbody>
            @for($i = 0; $i < $event->max_students; $i++)
                @php($student = $registration->students->get($i))
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        @if($student)
                            <input type="hidden" name="students[{{ $i }}][id]" value="{{ $student->id }}">
                        @endif
                        <input name="students[{{ $i }}][name]" value="{{ old("students.$i.name", optional($student)->name) }}">
                    </td>
                    <td><input name="students[{{ $i }}][matrix_no]" value="{{ old("students.$i.matrix_no", optional($student)->matrix_no) }}"></td>
                    <td><input name="students[{{ $i }}][ic_no]" value="{{ old("students.$i.ic_no", optional($student)->ic_no) }}"></td>
                    @if($event->usesHomeAwayJerseys())
                        <td><input name="students[{{ $i }}][jersey_no]" value="{{ old("students.$i.jersey_no", optional($student)->jersey_no) }}"></td>
                        <td><input name="students[{{ $i }}][jersey_no_away]" value="{{ old("students.$i.jersey_no_away", optional($student)->jersey_no_away) }}"></td>
                    @elseif($event->requires_jersey_no)
                        <td><input name="students[{{ $i }}][jersey_no]" value="{{ old("students.$i.jersey_no", optional($student)->jersey_no) }}"></td>
                    @endif
                    <td>
                        <input type="file" name="students[{{ $i }}][identity_document]" accept="application/pdf">
                        @if(optional($student)->identity_document_path)
                            <div class="muted">
                                Fail sedia ada:
                                <a href="{{ route('students.document', $student) }}">Lihat PDF</a>
                            </div>
                        @endif
                        @error("students.$i.identity_document")
                            <div class="errors">{{ $message }}</div>
                        @enderror
                    </td>
                </tr>
            @endfor
            </tbody>
        </table>
    </div>

    <div class="panel">
        <h2>Catatan</h2>
        <textarea name="notes">{{ old('notes', $registration->notes) }}</textarea>
    </div>

    <div class="actions">
        <button class="button" type="submit">Simpan Pendaftaran</button>
        <a class="button secondary" href="{{ route('dashboard') }}">Kembali</a>
    </div>
</form>
@endsection
