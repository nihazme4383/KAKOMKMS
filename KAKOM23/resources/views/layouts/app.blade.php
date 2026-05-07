<!doctype html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'KAKOM 23') }}</title>
    <style>
        :root {
            --bg: #f5f7fb;
            --panel: #ffffff;
            --ink: #172033;
            --muted: #657089;
            --line: #dfe5ee;
            --primary: #0f766e;
            --primary-dark: #115e59;
            --danger: #b91c1c;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
        }
        a { color: inherit; text-decoration: none; }
        .shell { display: grid; grid-template-columns: 280px 1fr; min-height: 100vh; }
        .sidebar {
            background: #111827;
            color: #e5e7eb;
            padding: 22px 16px;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }
        .brand { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
        .role { color: #a7b0c0; font-size: 13px; margin-bottom: 20px; line-height: 1.4; }
        .nav-label { color: #8ea0b8; font-size: 12px; letter-spacing: .06em; text-transform: uppercase; margin: 18px 10px 8px; }
        .nav-link {
            display: block;
            padding: 10px 12px;
            border-radius: 6px;
            color: #d5dbe5;
            margin-bottom: 3px;
            line-height: 1.25;
        }
        .nav-link:hover, .nav-link.active { background: #1f2937; color: #fff; }
        .event-chip {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 5px 10px;
            font-weight: 700;
            border: 1px solid transparent;
            white-space: nowrap;
        }
        .event-football { background: #dcfce7; border-color: #86efac; color: #166534; }
        .event-netball { background: #fce7f3; border-color: #f9a8d4; color: #9d174d; }
        .event-volleyball { background: #dbeafe; border-color: #93c5fd; color: #1d4ed8; }
        .event-takraw { background: #fef3c7; border-color: #fcd34d; color: #92400e; }
        .event-petanque { background: #ede9fe; border-color: #c4b5fd; color: #5b21b6; }
        .event-tennis { background: #ecfccb; border-color: #bef264; color: #3f6212; }
        .event-squash { background: #ffedd5; border-color: #fdba74; color: #9a3412; }
        .event-basketball { background: #fee2e2; border-color: #fca5a5; color: #991b1b; }
        .event-badminton { background: #ccfbf1; border-color: #5eead4; color: #0f766e; }
        .event-default { background: #f1f5f9; border-color: #cbd5e1; color: #334155; }
        .content { padding: 28px; min-width: 0; }
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 22px;
        }
        h1 { font-size: 26px; margin: 0 0 4px; }
        h2 { font-size: 18px; margin: 0 0 14px; }
        .muted { color: var(--muted); }
        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 18px;
            margin-bottom: 18px;
        }
        .button {
            border: 0;
            border-radius: 6px;
            background: var(--primary);
            color: white;
            padding: 10px 14px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
        }
        .button:hover { background: var(--primary-dark); }
        .button.secondary { background: #334155; }
        .button.link { background: transparent; color: #d5dbe5; padding: 8px 10px; }
        .alert {
            border-radius: 6px;
            padding: 12px 14px;
            margin-bottom: 16px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }
        .errors {
            border-radius: 6px;
            padding: 12px 14px;
            margin-bottom: 16px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: var(--danger);
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid var(--line); padding: 10px; vertical-align: top; text-align: left; }
        th { background: #f8fafc; font-size: 13px; color: #475569; }
        .table-scroll { overflow-x: auto; }
        .table-scroll table { min-width: 980px; }
        input, textarea {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 9px 10px;
            font: inherit;
            background: white;
        }
        input[type="checkbox"] {
            width: auto;
            margin: 0;
        }
        textarea { min-height: 90px; resize: vertical; }
        .checkbox-field {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            color: #0f766e;
        }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; }
        .stat { border: 1px solid var(--line); border-radius: 8px; padding: 14px; background: white; }
        .stat strong { display: block; font-size: 22px; margin-top: 4px; }
        .dashboard-page {
            margin: -28px;
            min-height: 100vh;
            padding: 28px;
            background:
                linear-gradient(135deg, #f0fdfa 0%, #eff6ff 42%, #fff7ed 100%);
        }
        .dashboard-page .topbar {
            background: #ffffffcc;
            border: 1px solid #dbeafe;
            border-radius: 8px;
            padding: 18px;
        }
        .panel.notice-panel { background: #fefce8; border-color: #fde68a; }
        .panel.registration-panel { background: #f8fafc; border-color: #cbd5e1; }
        .notice-table td:first-child { background: #fff7ed; width: 48%; }
        .notice-table td:nth-child(2) { background: #ffffff; font-weight: 700; color: #0f766e; }
        .notice-table td:nth-child(3) { background: #f0fdf4; width: 120px; text-align: center; }
        .dashboard-table th { background: #e0f2fe; color: #075985; }
        .dashboard-table tbody tr:nth-child(odd) td { background: #ffffff; }
        .dashboard-table tbody tr:nth-child(even) td { background: #f0f9ff; }
        .stat.stat-events { background: #ecfeff; border-color: #a5f3fc; }
        .stat.stat-forms { background: #f0fdf4; border-color: #bbf7d0; }
        .stat.stat-students { background: #fff7ed; border-color: #fed7aa; }
        .actions { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
        @media (max-width: 900px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar { position: static; height: auto; }
            .content { padding: 18px; }
            .dashboard-page { margin: -18px; padding: 18px; }
            .topbar { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
@if(isset($events) && isset($college))
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">KAKOM 23</div>
            <div class="role">{{ $college->name }}<br>{{ $college->isSecretariat() ? 'Akses Urusetia' : 'Akses Kolej' }}</div>
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
            @if($college->isSecretariat())
                @php($sidebarColleges = \App\Models\College::where('role', 'college')->orderBy('code')->get())
                <div class="nav-label">Kolej</div>
                @foreach($sidebarColleges as $sideCollege)
                    <a class="nav-link {{ isset($selectedCollege) && $selectedCollege->id === $sideCollege->id ? 'active' : '' }}" href="{{ route('colleges.events.menu', $sideCollege) }}">
                        {{ $sideCollege->code }}
                    </a>
                @endforeach
            @else
                <div class="nav-label">Acara</div>
                @foreach($events as $sideEvent)
                    <a class="nav-link {{ isset($event) && $event->id === $sideEvent->id ? 'active' : '' }}" href="{{ route('registrations.edit', $sideEvent) }}">
                        <span class="event-chip {{ $sideEvent->color_class }}">{{ $sideEvent->name }}</span>
                    </a>
                @endforeach
            @endif
            <form method="post" action="{{ route('logout') }}" style="margin-top: 18px;">
                @csrf
                <button class="button link" type="submit">Log keluar</button>
            </form>
        </aside>
        <main class="content">
            @yield('content')
        </main>
    </div>
@else
    @yield('content')
@endif
</body>
</html>
