import sys

file_path = r'c:\Users\userJ\Documents\fortain\routes\web.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

lecturer_routes = """
// Lecturer Defense Routes
Route::prefix('lecturer/defenses/internship')->middleware(['auth'])->name('lecturer.defenses.internship.')->group(function () {
    Route::get('/my-defenses', \App\Livewire\Lecturer\Defense\MyDefenses::class)->name('my-defenses');
    Route::get('/assessment/{defenseCase}', \App\Livewire\Lecturer\Defense\AssessmentForm::class)->name('assessment');
    Route::get('/suggestion/{defenseCase}', \App\Livewire\Lecturer\Defense\SuggestionForm::class)->name('suggestion');
});
"""

student_routes = """
// Student Defense Routes
Route::prefix('student/defenses/internship')->middleware(['auth:student'])->name('student.defenses.internship.')->group(function () {
    Route::get('/status', \App\Livewire\Student\Defense\DefenseStatus::class)->name('status');
    Route::get('/revision/{defenseCase}', \App\Livewire\Student\Defense\RevisionManager::class)->name('revision');
    Route::get('/result/{defenseCase}', \App\Livewire\Student\Defense\FinalResult::class)->name('result');
});
"""

if "lecturer.defenses.internship" not in content:
    content += lecturer_routes
    
if "student.defenses.internship" not in content:
    content += student_routes

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
