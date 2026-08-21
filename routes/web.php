<?php

use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\StudentAuthController;
use App\Livewire\Admin\ActivityTypeManager;
use App\Livewire\Admin\CalendarManager;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\EmailBlast;
use App\Livewire\Admin\FormFieldBuilder;
use App\Livewire\Admin\FormManager;
use App\Livewire\Admin\StudentManager;
use App\Livewire\Admin\SubmissionDetail;
use App\Livewire\Admin\SubmissionManager;
use App\Livewire\Admin\UserManager;
use App\Livewire\Admin\DocumentTemplate\Index as DocumentTemplateIndex;
use App\Livewire\Admin\DocumentTemplate\Form as DocumentTemplateForm;
use App\Livewire\Admin\DocumentTemplate\MasterHeader as DocumentTemplateMasterHeader;
use App\Http\Controllers\Admin\StudentRegistrationController;
use App\Http\Controllers\SubmissionFileController;
use App\Livewire\Student\StudentDashboard;
use App\Livewire\Student\StudentFormFiller;
use Illuminate\Support\Facades\Route;

// Public Home
Route::get('/', \App\Livewire\Public\LandingPage::class)->name('home');

// --- Student Authentication ---
Route::get('/login/mahasiswa', [StudentAuthController::class, 'showLogin'])->name('student.login');
Route::get('/auth/google/student/redirect', [StudentAuthController::class, 'redirectGoogle'])->name('student.google.login');
Route::get('/auth/google/student/callback', [StudentAuthController::class, 'callbackGoogle'])->name('student.google.callback');
Route::post('/logout', [StudentAuthController::class, 'logout'])->name('logout');

// --- Student Self-Registration ---
Route::get('/pendaftaran-mahasiswa', \App\Livewire\Public\StudentRegistration::class)->name('student.registration');
Route::get('/pendaftaran-mahasiswa/status', \App\Livewire\Public\RegistrationStatus::class)->name('student.registration.status');

// --- Admin Authentication ---
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'loginCredential'])->name('admin.login.submit');
Route::get('/auth/google/admin/redirect', [AdminAuthController::class, 'redirectGoogle'])->name('admin.google.login');
Route::get('/auth/google/admin/callback', [AdminAuthController::class, 'callbackGoogle'])->name('admin.google.callback');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// --- Admin Protected Routes ---
Route::prefix('admin')->middleware(['admin'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('admin.dashboard');
    Route::get('/pendaftaran-mahasiswa', \App\Livewire\Admin\StudentRegistrationManager::class)->name('admin.registrations');
    Route::get('/pendaftaran-mahasiswa/{registrationRequest}', [StudentRegistrationController::class, 'show'])->name('admin.registrations.show');
    Route::post('/pendaftaran-mahasiswa/{registrationRequest}/approve', [StudentRegistrationController::class, 'approve'])->name('admin.registrations.approve');
    Route::post('/pendaftaran-mahasiswa/{registrationRequest}/reject', [StudentRegistrationController::class, 'reject'])->name('admin.registrations.reject');
    Route::get('/activity-types', ActivityTypeManager::class)->name('admin.activity-types');
    Route::get('/forms', FormManager::class)->name('admin.forms');
    Route::get('/forms/{formId}/fields', FormFieldBuilder::class)->name('admin.forms.fields');
    Route::get('/students', StudentManager::class)->name('admin.students');
    Route::get('/submissions', SubmissionManager::class)->name('admin.submissions');
    Route::get('/submissions/{id}', SubmissionDetail::class)->name('admin.submissions.show');
    Route::get('/submissions/file/{file}', [SubmissionFileController::class, 'downloadAdmin'])->name('admin.submissions.file');
    Route::get('/calendar', CalendarManager::class)->name('admin.calendar');
    Route::get('/email-blast', \App\Livewire\Admin\EmailBlastManager::class)->name('admin.email-blast');
    Route::get('/email-blast/history', \App\Livewire\Admin\EmailBlastHistory::class)->name('admin.email-blast.history');
    Route::get('/email-blast/history/{id}', \App\Livewire\Admin\EmailBlastDetail::class)->name('admin.email-blast.detail');
    Route::get('/users', UserManager::class)->name('admin.users');
    Route::get('/lecturers', \App\Livewire\Admin\LecturerManager::class)->name('admin.lecturers');
    
    // Route::get('/document-templates/{type}/documents', [\App\Http\Controllers\Admin\DefenseDocumentTemplateController::class, 'index'])->name('admin.defenses.documents.index');
    Route::get('/defenses/document/preview/{case}/{type}', [\App\Http\Controllers\Admin\DocumentPreviewController::class, 'preview'])->name('admin.defenses.document.preview');
    
    // Document Builder Templates
    Route::prefix('document-templates')->name('admin.document-templates.')->group(function () {
        Route::get('/', DocumentTemplateIndex::class)->name('index');
        Route::get('/master-header', DocumentTemplateMasterHeader::class)->name('master-header');
        Route::get('/assets', \App\Livewire\Admin\DocumentTemplate\AssetManager::class)->name('assets');
        Route::get('/create', DocumentTemplateForm::class)->name('create');
        Route::get('/{id}/edit', DocumentTemplateForm::class)->name('edit');
        Route::post('/upload-image', [\App\Http\Controllers\Admin\EditorImageController::class, 'store'])->name('upload-image');
    });

    // Internship Letters
    Route::get('/internship-letters', \App\Livewire\Admin\InternshipLetter\RequestManager::class)->name('admin.internship-letters.index');
    Route::get('/internship-letters/settings', \App\Livewire\Admin\InternshipLetter\TemplateSettings::class)->name('admin.internship-letters.settings');
    Route::get('/internship-letters/{id}', \App\Livewire\Admin\InternshipLetter\RequestDetail::class)->name('admin.internship-letters.show');

    // Signature Requests
    Route::get('/signature-requests', \App\Livewire\Admin\SignatureRequestManager::class)->name('admin.signature-requests.index');
    Route::get('/file-download', [\App\Http\Controllers\Admin\FileDownloadController::class, 'downloadPrivate'])->name('admin.file.download');

    // Defense Management - Internship
    Route::prefix('defenses/internship')->name('admin.defenses.internship.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Admin\Defense\Dashboard::class)->name('dashboard');
        Route::get('/participants', \App\Livewire\Admin\Defense\ParticipantManager::class)->name('participants');
        Route::get('/schedule', \App\Livewire\Admin\Defense\ScheduleManager::class)->name('schedule');
                Route::get('/mentor-score', \App\Livewire\Admin\Defense\MentorScoreInput::class)->name('mentor-score');
        Route::get('/{defense}/mentor-document/preview', [\App\Http\Controllers\MentorDocumentController::class, 'preview'])->name('mentor-document.preview');
        Route::get('/{defense}/mentor-document/download', [\App\Http\Controllers\MentorDocumentController::class, 'download'])->name('mentor-document.download');
        
        // Generated Documents Download
        Route::get('/documents/{id}/download', [\App\Http\Controllers\GeneratedDocumentController::class, 'download'])->name('documents.download');
                Route::get('/recap', \App\Livewire\Admin\Defense\RecapAndDocuments::class)->name('recap');
        Route::get('/score/{caseId}/{role}', \App\Livewire\Admin\Defense\AdminAssessmentForm::class)->name('score');
    });
});

// --- Student Protected Routes ---
Route::middleware(['student'])->group(function () {
    Route::get('/dashboard', StudentDashboard::class)->name('student.dashboard');
    Route::get('/form/{slug}', StudentFormFiller::class)->name('student.forms.show');
    Route::get('/submissions/file/{file}', [SubmissionFileController::class, 'downloadStudent'])->name('student.submissions.file');

    // Internship Letters
    Route::get('/internship-letters', \App\Livewire\Student\InternshipLetter\RequestList::class)->name('student.internship-letters.index');
    Route::get('/internship-letters/create', \App\Livewire\Student\InternshipLetter\RequestForm::class)->name('student.internship-letters.create');
    Route::get('/internship-letters/{id}/edit', \App\Livewire\Student\InternshipLetter\RequestForm::class)->name('student.internship-letters.edit');

    // Signature Requests
    Route::get('/signature-requests', \App\Livewire\Student\SignatureRequestList::class)->name('student.signature-requests.index');
    Route::get('/signature-requests/create', \App\Livewire\Student\SignatureRequestForm::class)->name('student.signature-requests.create');
    Route::get('/signature-requests/{id}', \App\Livewire\Student\SignatureRequestForm::class)->name('student.signature-requests.show');
});


// Lecturer Defense Routes
Route::prefix('lecturer/defenses/internship')->middleware(['auth'])->name('lecturer.defenses.internship.')->group(function () {
    Route::get('/my-defenses', \App\Livewire\Lecturer\Defense\MyDefenses::class)->name('my-defenses');
    Route::get('/assessment/{defenseCase}', \App\Livewire\Lecturer\Defense\AssessmentForm::class)->name('assessment');
    Route::get('/suggestion/{defenseCase}', \App\Livewire\Lecturer\Defense\SuggestionForm::class)->name('suggestion');
});

// Lecturer General Routes
Route::prefix('lecturer')->middleware(['auth'])->name('lecturer.')->group(function () {
    Route::get('/profile', \App\Livewire\Lecturer\Profile::class)->name('profile');
});

// Student Defense Routes
Route::prefix('student/defenses/internship')->middleware(['auth:student'])->name('student.defenses.internship.')->group(function () {
    Route::get('/status', \App\Livewire\Student\Defense\DefenseStatus::class)->name('status');
    Route::get('/revision/{defenseCase}', \App\Livewire\Student\Defense\RevisionManager::class)->name('revision');
    Route::get('/result/{defenseCase}', \App\Livewire\Student\Defense\FinalResult::class)->name('result');
});
