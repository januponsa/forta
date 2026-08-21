import sys

file_path = r'c:\Users\userJ\Documents\fortain\routes\web.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

replacement = """        Route::get('/recap', \App\Livewire\Admin\Defense\RecapAndDocuments::class)->name('recap');
        Route::get('/score/{caseId}/{role}', \App\Livewire\Admin\Defense\AdminAssessmentForm::class)->name('score');"""

content = content.replace("Route::get('/recap', \App\Livewire\Admin\Defense\RecapAndDocuments::class)->name('recap');", replacement)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
