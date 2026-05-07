<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\EventConfirmation;
use App\Models\SportEvent;
use App\Models\TeamRegistration;
use App\Models\TeamStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RegistrationController extends Controller
{
    private function currentCollege(Request $request)
    {
        $collegeId = $request->session()->get('college_id');

        abort_unless($collegeId, 403);

        return College::findOrFail($collegeId);
    }

    private function events()
    {
        return SportEvent::orderBy('sort_order')->get();
    }

    public function dashboard(Request $request)
    {
        $college = $this->currentCollege($request);
        $events = $this->events();

        $query = TeamRegistration::with(['college', 'event', 'students']);

        if (! $college->isSecretariat()) {
            $query->where('college_id', $college->id);
        }

        $registrations = $query->latest()->get();
        $colleges = collect();

        if ($college->isSecretariat()) {
            $colleges = College::where('role', 'college')
                ->withCount(['eventConfirmations', 'registrations'])
                ->orderBy('name')
                ->get();
        }

        return view('dashboard', compact('college', 'events', 'registrations', 'colleges'));
    }

    public function showCollegeEvents(Request $request, College $selectedCollege)
    {
        $college = $this->currentCollege($request);
        abort_unless($college->isSecretariat(), 403);
        abort_if($selectedCollege->isSecretariat(), 404);

        $events = $this->events();
        $selectedCollege->load([
            'eventConfirmations.event',
            'registrations.event',
            'registrations.students',
        ]);

        $registrations = $selectedCollege->registrations->keyBy('sport_event_id');
        $confirmedEvents = $selectedCollege->eventConfirmations
            ->sortBy(fn($confirmation) => optional($confirmation->event)->sort_order)
            ->values();

        return view('colleges.events', compact('college', 'events', 'selectedCollege', 'confirmedEvents', 'registrations'));
    }

    public function showCollegeEventMenu(Request $request, College $selectedCollege)
    {
        $college = $this->currentCollege($request);
        abort_unless($college->isSecretariat(), 403);
        abort_if($selectedCollege->isSecretariat(), 404);

        $events = $this->events();
        $registrations = TeamRegistration::with(['event', 'students'])
            ->where('college_id', $selectedCollege->id)
            ->get()
            ->keyBy('sport_event_id');

        return view('colleges.event-menu', compact('college', 'events', 'selectedCollege', 'registrations'));
    }

    public function exportCollegeEventsPdf(Request $request, College $selectedCollege)
    {
        $college = $this->currentCollege($request);
        abort_unless($college->isSecretariat(), 403);
        abort_if($selectedCollege->isSecretariat(), 404);

        $selectedCollege->load([
            'eventConfirmations.event',
            'registrations.event',
            'registrations.students',
        ]);

        $registrations = $selectedCollege->registrations->keyBy('sport_event_id');
        $confirmedEvents = $selectedCollege->eventConfirmations
            ->sortBy(fn($confirmation) => optional($confirmation->event)->sort_order)
            ->values();

        $lines = [
            'KAKOM 23 - Pengesahan Acara Kolej',
            'Kolej: ' . $selectedCollege->name,
            'Tarikh eksport: ' . now()->format('d/m/Y h:i A'),
            '',
            'Bil  Acara                              Had   Pelajar  Status Borang',
            '--------------------------------------------------------------------',
        ];

        foreach ($confirmedEvents as $index => $confirmation) {
            $event = $confirmation->event;

            if (! $event) {
                continue;
            }

            $registration = $registrations->get($event->id);
            $lines[] = sprintf(
                '%-4s %-34s %-5s %-8s %s',
                $index + 1,
                $this->pdfColumn($event->name, 34),
                $event->max_students,
                $registration ? $registration->students->count() : 0,
                $registration ? 'Borang telah dibuat' : 'Belum ada borang'
            );
        }

        if ($confirmedEvents->isEmpty()) {
            $lines[] = 'Kolej ini belum mengesahkan penyertaan acara.';
        }

        $fileName = 'acara-' . strtolower(preg_replace('/[^A-Za-z0-9]+/', '-', $selectedCollege->code)) . '.pdf';

        return $this->pdfResponse($lines, $fileName);
    }

    public function exportCollegeEventsExcel(Request $request, College $selectedCollege)
    {
        $college = $this->currentCollege($request);
        abort_unless($college->isSecretariat(), 403);
        abort_if($selectedCollege->isSecretariat(), 404);

        $selectedCollege->load([
            'eventConfirmations.event',
            'registrations.event',
            'registrations.students',
        ]);

        $registrations = $selectedCollege->registrations->keyBy('sport_event_id');
        $confirmedEvents = $selectedCollege->eventConfirmations
            ->sortBy(fn($confirmation) => optional($confirmation->event)->sort_order)
            ->values();

        $rows = [
            ['Kolej', $selectedCollege->name],
            ['Tarikh Eksport', now()->format('d/m/Y h:i A')],
            [],
            ['Bil', 'Acara', 'Had Pelajar', 'Pelajar Daftar', 'Status Borang'],
        ];

        foreach ($confirmedEvents as $index => $confirmation) {
            $event = $confirmation->event;

            if (! $event) {
                continue;
            }

            $registration = $registrations->get($event->id);
            $rows[] = [
                $index + 1,
                $event->name,
                $event->max_students,
                $registration ? $registration->students->count() : 0,
                $registration ? 'Borang telah dibuat' : 'Belum ada borang',
            ];
        }

        $fileName = 'acara-' . strtolower(preg_replace('/[^A-Za-z0-9]+/', '-', $selectedCollege->code)) . '.csv';

        return $this->csvResponse($rows, $fileName);
    }

    public function exportCollegeEventNamesExcel(Request $request, College $selectedCollege)
    {
        $college = $this->currentCollege($request);
        abort_unless($college->isSecretariat(), 403);
        abort_if($selectedCollege->isSecretariat(), 404);

        $selectedCollege->load('eventConfirmations.event');

        $confirmedEvents = $selectedCollege->eventConfirmations
            ->sortBy(fn($confirmation) => optional($confirmation->event)->sort_order)
            ->values();

        $rows = [
            ['Kolej', $selectedCollege->name],
            ['Tarikh Eksport', now()->format('d/m/Y h:i A')],
            [],
            ['Bil', 'Nama Acara'],
        ];

        foreach ($confirmedEvents as $index => $confirmation) {
            if (! $confirmation->event) {
                continue;
            }

            $rows[] = [
                $index + 1,
                $confirmation->event->name,
            ];
        }

        $fileName = 'nama-acara-' . strtolower(preg_replace('/[^A-Za-z0-9]+/', '-', $selectedCollege->code)) . '.csv';

        return $this->csvResponse($rows, $fileName);
    }

    public function edit(Request $request, SportEvent $event)
    {
        $college = $this->currentCollege($request);
        $events = $this->events();

        if ($college->isSecretariat()) {
            $registrations = TeamRegistration::with(['college', 'students'])
                ->where('sport_event_id', $event->id)
                ->latest()
                ->get();

            return view('registrations.event', compact('college', 'events', 'event', 'registrations'));
        }

        $registration = TeamRegistration::with(['officials', 'students'])
            ->firstOrCreate([
                'college_id' => $college->id,
                'sport_event_id' => $event->id,
            ]);

        $officials = $registration->officials->keyBy('role');

        return view('registrations.form', compact('college', 'events', 'event', 'registration', 'officials'));
    }

    public function exportDashboardPdf(Request $request)
    {
        $college = $this->currentCollege($request);

        if ($college->isSecretariat()) {
            $colleges = College::where('role', 'college')
                ->withCount(['eventConfirmations', 'registrations'])
                ->orderBy('name')
                ->get();

            $lines = [
                'KAKOM 23 - Dashboard Urusetia',
                'Tarikh eksport: ' . now()->format('d/m/Y h:i A'),
                '',
                'Bil  Kolej                              Acara  Borang',
                '---------------------------------------------------------------',
            ];

            foreach ($colleges as $index => $listedCollege) {
                $lines[] = sprintf(
                    '%-4s %-34s %-6s %s',
                    $index + 1,
                    $this->pdfColumn($listedCollege->name, 34),
                    $listedCollege->event_confirmations_count,
                    $listedCollege->registrations_count
                );
            }

            return $this->pdfResponse($lines, 'dashboard-urusetia.pdf');
        }

        $registrations = TeamRegistration::with(['event', 'students'])
            ->where('college_id', $college->id)
            ->latest()
            ->get();

        $lines = [
            'KAKOM 23 - Dashboard Kolej',
            'Kolej: ' . $college->name,
            'Tarikh eksport: ' . now()->format('d/m/Y h:i A'),
            '',
            'Bil  Acara                              Pelajar  Kemaskini',
            '---------------------------------------------------------------',
        ];

        foreach ($registrations as $index => $registration) {
            $lines[] = sprintf(
                '%-4s %-34s %-8s %s',
                $index + 1,
                $this->pdfColumn($registration->event->name, 34),
                $registration->students->count(),
                $registration->updated_at->format('d/m/Y h:i A')
            );
        }

        if ($registrations->isEmpty()) {
            $lines[] = 'Tiada pendaftaran lagi.';
        }

        return $this->pdfResponse($lines, 'dashboard-' . strtolower($college->code) . '.pdf');
    }

    public function exportDashboardExcel(Request $request)
    {
        $college = $this->currentCollege($request);

        if ($college->isSecretariat()) {
            $colleges = College::where('role', 'college')
                ->withCount(['eventConfirmations', 'registrations'])
                ->orderBy('name')
                ->get();

            $rows = [
                ['Dashboard Urusetia'],
                ['Tarikh Eksport', now()->format('d/m/Y h:i A')],
                [],
                ['Bil', 'Kolej', 'Acara Disahkan', 'Borang Dihantar'],
            ];

            foreach ($colleges as $index => $listedCollege) {
                $rows[] = [
                    $index + 1,
                    $listedCollege->name,
                    $listedCollege->event_confirmations_count,
                    $listedCollege->registrations_count,
                ];
            }

            return $this->csvResponse($rows, 'dashboard-urusetia.csv');
        }

        $registrations = TeamRegistration::with(['event', 'students'])
            ->where('college_id', $college->id)
            ->latest()
            ->get();

        $rows = [
            ['Kolej', $college->name],
            ['Tarikh Eksport', now()->format('d/m/Y h:i A')],
            [],
            ['Bil', 'Acara', 'Pelajar', 'Kemaskini'],
        ];

        foreach ($registrations as $index => $registration) {
            $rows[] = [
                $index + 1,
                $registration->event->name,
                $registration->students->count(),
                $registration->updated_at->format('d/m/Y h:i A'),
            ];
        }

        return $this->csvResponse($rows, 'dashboard-' . strtolower($college->code) . '.csv');
    }

    public function exportEventRegistrationPdf(Request $request, SportEvent $event)
    {
        $college = $this->currentCollege($request);
        abort_if($college->isSecretariat(), 403);

        $registration = TeamRegistration::with(['officials', 'students'])
            ->firstOrCreate([
                'college_id' => $college->id,
                'sport_event_id' => $event->id,
            ]);

        $officials = $registration->officials->keyBy('role');
        $lines = [
            'KAKOM 23 - Borang Pendaftaran Acara',
            'Kolej: ' . $college->name,
            'Acara: ' . $event->name,
            'Tarikh eksport: ' . now()->format('d/m/Y h:i A'),
            '',
            'Pegawai Pasukan',
            'Peranan              Nama                     No KP           Telefon',
            '--------------------------------------------------------------------',
        ];

        foreach ($this->officialRoles($event) as $role => $label) {
            $official = $officials->get($role);
            $lines[] = sprintf(
                '%-20s %-24s %-15s %s',
                $this->pdfColumn($label, 20),
                $this->pdfColumn(optional($official)->name ?: '-', 24),
                optional($official)->ic_no ?: '-',
                optional($official)->phone_no ?: '-'
            );
        }

        $lines[] = '';
        $lines[] = 'Peserta';
        $lines[] = $event->usesHomeAwayJerseys()
            ? 'Bil  Nama                     Matrik       No KP           Home  Away'
            : ($event->requires_jersey_no
                ? 'Bil  Nama                     Matrik       No KP           Jersi'
                : 'Bil  Nama                     Matrik       No KP');
        $lines[] = '--------------------------------------------------------------------';

        foreach ($registration->students as $index => $student) {
            if ($event->usesHomeAwayJerseys()) {
                $lines[] = sprintf('%-4s %-24s %-12s %-15s %-5s %s', $index + 1, $this->pdfColumn($student->name, 24), $this->pdfColumn($student->matrix_no, 12), $student->ic_no ?: '-', $student->jersey_no ?: '-', $student->jersey_no_away ?: '-');
            } elseif ($event->requires_jersey_no) {
                $lines[] = sprintf('%-4s %-24s %-12s %-15s %s', $index + 1, $this->pdfColumn($student->name, 24), $this->pdfColumn($student->matrix_no, 12), $student->ic_no ?: '-', $student->jersey_no ?: '-');
            } else {
                $lines[] = sprintf('%-4s %-24s %-12s %s', $index + 1, $this->pdfColumn($student->name, 24), $this->pdfColumn($student->matrix_no, 12), $student->ic_no ?: '-');
            }
        }

        if ($registration->students->isEmpty()) {
            $lines[] = 'Tiada pelajar direkodkan.';
        }

        return $this->pdfResponse($lines, $event->slug . '-' . strtolower($college->code) . '.pdf');
    }

    public function exportEventRegistrationExcel(Request $request, SportEvent $event)
    {
        $college = $this->currentCollege($request);

        if ($college->isSecretariat()) {
            $registrations = TeamRegistration::with(['college', 'students'])
                ->where('sport_event_id', $event->id)
                ->latest()
                ->get();

            $rows = [
                ['Acara', $event->name],
                ['Tarikh Eksport', now()->format('d/m/Y h:i A')],
                [],
                ['Bil', 'Kolej', 'Pelajar', 'Kemaskini'],
            ];

            foreach ($registrations as $index => $registration) {
                $rows[] = [
                    $index + 1,
                    $registration->college->name,
                    $registration->students->count(),
                    $registration->updated_at->format('d/m/Y h:i A'),
                ];
            }

            return $this->csvResponse($rows, 'senarai-borang-' . $event->slug . '.csv');
        }

        $registration = TeamRegistration::with(['officials', 'students'])
            ->firstOrCreate([
                'college_id' => $college->id,
                'sport_event_id' => $event->id,
            ]);

        $officials = $registration->officials->keyBy('role');
        $rows = [
            ['Kolej', $college->name],
            ['Acara', $event->name],
            ['Tarikh Eksport', now()->format('d/m/Y h:i A')],
            [],
            ['Pegawai Pasukan'],
            ['Peranan', 'Nama', 'No Kad Pengenalan', 'Jawatan', 'No Telefon'],
        ];

        foreach ($this->officialRoles($event) as $role => $label) {
            $official = $officials->get($role);
            $rows[] = [$label, optional($official)->name ?: '', optional($official)->ic_no ?: '', optional($official)->position ?: '', optional($official)->phone_no ?: ''];
        }

        $rows[] = [];
        $rows[] = ['Peserta'];
        $rows[] = $event->usesHomeAwayJerseys()
            ? ['Bil', 'Nama', 'No Matrik', 'No Kad Pengenalan', 'No Jersi Home', 'No Jersi Away']
            : ($event->requires_jersey_no
                ? ['Bil', 'Nama', 'No Matrik', 'No Kad Pengenalan', 'No Jersi']
                : ['Bil', 'Nama', 'No Matrik', 'No Kad Pengenalan']);

        foreach ($registration->students as $index => $student) {
            $row = [$index + 1, $student->name, $student->matrix_no, $student->ic_no];

            if ($event->usesHomeAwayJerseys()) {
                $row[] = $student->jersey_no;
                $row[] = $student->jersey_no_away;
            } elseif ($event->requires_jersey_no) {
                $row[] = $student->jersey_no;
            }

            $rows[] = $row;
        }

        return $this->csvResponse($rows, $event->slug . '-' . strtolower($college->code) . '.csv');
    }

    public function update(Request $request, SportEvent $event)
    {
        $college = $this->currentCollege($request);
        abort_if($college->isSecretariat(), 403, 'Urusetia hanya boleh melihat data pendaftaran.');

        $validated = $request->validate([
            'officials.coach_1.name' => ['nullable', 'string', 'max:255'],
            'officials.coach_1.ic_no' => ['nullable', 'string', 'max:30'],
            'officials.coach_1.position' => ['nullable', 'string', 'max:255'],
            'officials.coach_1.phone_no' => ['nullable', 'string', 'max:30'],
            'officials.coach_2.name' => ['nullable', 'string', 'max:255'],
            'officials.coach_2.ic_no' => ['nullable', 'string', 'max:30'],
            'officials.coach_2.position' => ['nullable', 'string', 'max:255'],
            'officials.coach_2.phone_no' => ['nullable', 'string', 'max:30'],
            'officials.manager.name' => ['nullable', 'string', 'max:255'],
            'officials.manager.ic_no' => ['nullable', 'string', 'max:30'],
            'officials.manager.position' => ['nullable', 'string', 'max:255'],
            'officials.manager.phone_no' => ['nullable', 'string', 'max:30'],
            'students' => ['nullable', 'array', 'max:' . $event->max_students],
            'students.*.id' => ['nullable', 'integer'],
            'students.*.name' => ['nullable', 'string', 'max:255'],
            'students.*.matrix_no' => ['nullable', 'string', 'max:100'],
            'students.*.ic_no' => ['nullable', 'string', 'max:30'],
            'students.*.jersey_no' => ['nullable', 'string', 'max:20'],
            'students.*.jersey_no_away' => ['nullable', 'string', 'max:20'],
            'students.*.identity_document' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($request, $college, $event, $validated) {
            $registration = TeamRegistration::updateOrCreate(
                ['college_id' => $college->id, 'sport_event_id' => $event->id],
                ['notes' => $validated['notes'] ?? null]
            );

            foreach (['coach_1', 'coach_2', 'manager'] as $role) {
                $official = $validated['officials'][$role] ?? [];
                $registration->officials()->updateOrCreate(
                    ['role' => $role],
                    [
                        'name' => $official['name'] ?? null,
                        'ic_no' => $official['ic_no'] ?? null,
                        'position' => $official['position'] ?? null,
                        'phone_no' => $official['phone_no'] ?? null,
                    ]
                );
            }

            $keptStudentIds = [];

            foreach (($validated['students'] ?? []) as $index => $student) {
                $studentModel = null;

                if (! empty($student['id'])) {
                    $studentModel = $registration->students()
                        ->whereKey($student['id'])
                        ->first();
                }

                $hasUploadedDocument = $request->hasFile("students.$index.identity_document");
                $hasExistingDocument = $studentModel && $studentModel->identity_document_path;
                $isBlank = empty($student['name'])
                    && empty($student['matrix_no'])
                    && empty($student['ic_no'])
                    && empty($student['jersey_no'])
                    && empty($student['jersey_no_away'])
                    && ! $hasUploadedDocument
                    && ! $hasExistingDocument;

                if ($isBlank) {
                    if ($studentModel) {
                        $studentModel->delete();
                    }

                    continue;
                }

                $studentData = [
                    'name' => $student['name'] ?? '',
                    'matrix_no' => $student['matrix_no'] ?? '',
                    'ic_no' => $student['ic_no'] ?? null,
                    'jersey_no' => $event->requires_jersey_no ? ($student['jersey_no'] ?? null) : null,
                    'jersey_no_away' => $event->usesHomeAwayJerseys() ? ($student['jersey_no_away'] ?? null) : null,
                ];

                if ($hasUploadedDocument) {
                    if ($studentModel && $studentModel->identity_document_path) {
                        Storage::delete($studentModel->identity_document_path);
                    }

                    $studentData['identity_document_path'] = $request
                        ->file("students.$index.identity_document")
                        ->store('student-documents');
                }

                if ($studentModel) {
                    $studentModel->update($studentData);
                } else {
                    $studentModel = $registration->students()->create($studentData);
                }

                $keptStudentIds[] = $studentModel->id;
            }

            $registration->students()
                ->whereNotIn('id', $keptStudentIds)
                ->get()
                ->each(function (TeamStudent $student) {
                    if ($student->identity_document_path) {
                        Storage::delete($student->identity_document_path);
                    }

                    $student->delete();
                });
        });

        return redirect()
            ->route('registrations.edit', $event)
            ->with('status', 'Pendaftaran berjaya disimpan.');
    }

    public function editEventConfirmation(Request $request)
    {
        $college = $this->currentCollege($request);

        $events = $this->events();

        if ($college->isSecretariat()) {
            $colleges = College::where('role', 'college')
                ->with('eventConfirmations')
                ->orderBy('name')
                ->get();

            return view('event-confirmations.index', compact('college', 'events', 'colleges'));
        }

        $confirmedEventIds = $college->eventConfirmations()
            ->pluck('sport_event_id')
            ->all();

        return view('event-confirmations.edit', compact('college', 'events', 'confirmedEventIds'));
    }

    public function updateEventConfirmation(Request $request)
    {
        $college = $this->currentCollege($request);
        abort_if($college->isSecretariat(), 403, 'Urusetia hanya boleh melihat data pendaftaran.');

        $validated = $request->validate([
            'events' => ['nullable', 'array'],
            'events.*' => ['integer', 'exists:sport_events,id'],
        ]);

        $selectedEventIds = array_values(array_unique($validated['events'] ?? []));

        DB::transaction(function () use ($college, $selectedEventIds) {
            $college->eventConfirmations()
                ->whereNotIn('sport_event_id', $selectedEventIds)
                ->delete();

            foreach ($selectedEventIds as $eventId) {
                EventConfirmation::updateOrCreate([
                    'college_id' => $college->id,
                    'sport_event_id' => $eventId,
                ]);
            }
        });

        return redirect()
            ->route('event-confirmations.edit')
            ->with('status', 'Pengesahan acara berjaya disimpan.');
    }

    public function show(Request $request, TeamRegistration $registration)
    {
        $college = $this->currentCollege($request);
        abort_unless($college->isSecretariat() || $registration->college_id === $college->id, 403);

        $events = $this->events();
        $registration->load(['college', 'event', 'officials', 'students']);
        $officials = $registration->officials->keyBy('role');

        return view('registrations.show', compact('college', 'events', 'registration', 'officials'));
    }

    public function exportRegistrationExcel(Request $request, TeamRegistration $registration)
    {
        $college = $this->currentCollege($request);
        abort_unless($college->isSecretariat() || $registration->college_id === $college->id, 403);

        $registration->load(['college', 'event', 'officials', 'students']);
        $officials = $registration->officials->keyBy('role');
        $event = $registration->event;

        $rows = [
            ['Kolej', $registration->college->name],
            ['Acara', $event->name],
            ['Tarikh Eksport', now()->format('d/m/Y h:i A')],
            [],
            ['Pegawai Pasukan'],
            ['Peranan', 'Nama', 'No Kad Pengenalan', 'Jawatan', 'No Telefon'],
        ];

        foreach ($this->officialRoles($event) as $role => $label) {
            $official = $officials->get($role);
            $rows[] = [
                $label,
                optional($official)->name ?: '',
                optional($official)->ic_no ?: '',
                optional($official)->position ?: '',
                optional($official)->phone_no ?: '',
            ];
        }

        $rows[] = [];
        $rows[] = ['Peserta'];
        $rows[] = $event->usesHomeAwayJerseys()
            ? ['Bil', 'Nama', 'No Matrik', 'No Kad Pengenalan', 'No Jersi Home', 'No Jersi Away', 'Dokumen PDF']
            : ($event->requires_jersey_no
                ? ['Bil', 'Nama', 'No Matrik', 'No Kad Pengenalan', 'No Jersi', 'Dokumen PDF']
                : ['Bil', 'Nama', 'No Matrik', 'No Kad Pengenalan', 'Dokumen PDF']);

        foreach ($registration->students as $index => $student) {
            $row = [
                $index + 1,
                $student->name,
                $student->matrix_no,
                $student->ic_no,
            ];

            if ($event->usesHomeAwayJerseys()) {
                $row[] = $student->jersey_no;
                $row[] = $student->jersey_no_away;
            } elseif ($event->requires_jersey_no) {
                $row[] = $student->jersey_no;
            }

            $row[] = $student->identity_document_path ? 'Ada' : 'Tiada';
            $rows[] = $row;
        }

        $fileName = 'pendaftaran-' . $event->slug . '-' . strtolower($registration->college->code) . '.csv';

        return $this->csvResponse($rows, $fileName);
    }

    public function downloadStudentDocument(Request $request, TeamStudent $student)
    {
        $college = $this->currentCollege($request);
        $student->load('registration.college');

        abort_unless($college->isSecretariat() || $student->registration->college_id === $college->id, 403);
        abort_unless($student->identity_document_path && Storage::exists($student->identity_document_path), 404);

        $fileName = $student->matrix_no
            ? $student->matrix_no . '-dokumen-pengenalan.pdf'
            : 'dokumen-pengenalan.pdf';

        return Storage::download($student->identity_document_path, $fileName);
    }

    private function officialRoles(SportEvent $event)
    {
        if ($event->slug === 'bola-sepak') {
            return ['manager' => 'Pengurus', 'coach_1' => 'Jurulatih 1', 'coach_2' => 'Fisioterapi'];
        }

        return ['manager' => 'Pengurus', 'coach_1' => 'Jurulatih 1'];
    }

    private function pdfResponse(array $lines, $fileName)
    {
        return response($this->makeSimplePdf($lines), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    private function csvResponse(array $rows, $fileName)
    {
        $csv = "\xEF\xBB\xBF";

        foreach ($rows as $row) {
            $csv .= implode(',', array_map(function ($value) {
                $value = (string) $value;
                $value = str_replace('"', '""', $value);

                return '"' . $value . '"';
            }, $row)) . "\r\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    private function pdfColumn($value, $length)
    {
        $value = preg_replace('/\s+/', ' ', trim($value));

        if (strlen($value) <= $length) {
            return $value;
        }

        return substr($value, 0, $length - 3) . '...';
    }

    private function makeSimplePdf(array $lines)
    {
        $content = "BT\n";
        $content .= "/F1 16 Tf\n50 790 Td (" . $this->escapePdfText($lines[0] ?? '') . ") Tj\n";
        $content .= "/F1 10 Tf\n0 -24 Td\n";

        foreach (array_slice($lines, 1) as $line) {
            $content .= "(" . $this->escapePdfText($line) . ") Tj\n0 -16 Td\n";
        }

        $content .= "ET";

        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Courier >>',
            '<< /Length ' . strlen($content) . " >>\nstream\n" . $content . "\nendstream",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= str_pad((string) $offsets[$i], 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefOffset . "\n%%EOF";

        return $pdf;
    }

    private function escapePdfText($text)
    {
        $text = str_replace(["\\", "(", ")"], ["\\\\", "\\(", "\\)"], $text);

        return preg_replace('/[^\x20-\x7E]/', '', $text);
    }
}
