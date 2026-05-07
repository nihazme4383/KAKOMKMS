<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('college.access')->group(function () {
    Route::get('/dashboard', [RegistrationController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/pdf', [RegistrationController::class, 'exportDashboardPdf'])->name('dashboard.pdf');
    Route::get('/dashboard/excel', [RegistrationController::class, 'exportDashboardExcel'])->name('dashboard.excel');
    Route::get('/kolej/{selectedCollege}/acara', [RegistrationController::class, 'showCollegeEvents'])->name('colleges.events');
    Route::get('/kolej/{selectedCollege}/menu-acara', [RegistrationController::class, 'showCollegeEventMenu'])->name('colleges.events.menu');
    Route::get('/kolej/{selectedCollege}/acara/pdf', [RegistrationController::class, 'exportCollegeEventsPdf'])->name('colleges.events.pdf');
    Route::get('/kolej/{selectedCollege}/acara/excel', [RegistrationController::class, 'exportCollegeEventsExcel'])->name('colleges.events.excel');
    Route::get('/kolej/{selectedCollege}/acara/nama-excel', [RegistrationController::class, 'exportCollegeEventNamesExcel'])->name('colleges.events.names.excel');
    Route::get('/pengesahan-acara', [RegistrationController::class, 'editEventConfirmation'])->name('event-confirmations.edit');
    Route::post('/pengesahan-acara', [RegistrationController::class, 'updateEventConfirmation'])->name('event-confirmations.update');
    Route::get('/acara/{event:slug}', [RegistrationController::class, 'edit'])->name('registrations.edit');
    Route::post('/acara/{event:slug}', [RegistrationController::class, 'update'])->name('registrations.update');
    Route::get('/acara/{event:slug}/pdf', [RegistrationController::class, 'exportEventRegistrationPdf'])->name('registrations.pdf');
    Route::get('/acara/{event:slug}/excel', [RegistrationController::class, 'exportEventRegistrationExcel'])->name('registrations.excel');
    Route::get('/pendaftaran/{registration}', [RegistrationController::class, 'show'])->name('registrations.show');
    Route::get('/pendaftaran/{registration}/excel', [RegistrationController::class, 'exportRegistrationExcel'])->name('registrations.show.excel');
    Route::get('/pelajar/{student}/dokumen', [RegistrationController::class, 'downloadStudentDocument'])->name('students.document');
});
