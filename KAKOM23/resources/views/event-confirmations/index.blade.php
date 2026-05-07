@extends('layouts.app')

@section('content')
<div class="topbar">
    <div>
        <h1>Semakan Pengesahan Acara</h1>
        <div class="muted">Status pengesahan penyertaan acara mengikut kolej.</div>
    </div>
</div>

<div class="panel">
    <h2>Ringkasan Pengesahan</h2>
    <div class="table-scroll">
    <table>
        <thead>
        <tr>
            <th style="width:70px;">Bil</th>
            <th>Kolej</th>
            @foreach($events as $event)
                <th><span class="event-chip {{ $event->color_class }}">{{ $event->name }}</span></th>
            @endforeach
            <th style="width:120px;">Jumlah</th>
        </tr>
        </thead>
        <tbody>
        @foreach($colleges as $listedCollege)
            @php($confirmedEventIds = $listedCollege->eventConfirmations->pluck('sport_event_id')->all())
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><strong>{{ $listedCollege->name }}</strong></td>
                @foreach($events as $event)
                    <td style="text-align:center;">
                        @if(in_array($event->id, $confirmedEventIds))
                            &#10003;
                        @else
                            -
                        @endif
                    </td>
                @endforeach
                <td><strong>{{ count($confirmedEventIds) }}</strong></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>
@endsection
