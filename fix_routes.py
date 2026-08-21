import sys

file_path = r'c:\Users\userJ\Documents\fortain\routes\web.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

replacement = """        Route::get('/mentor-score', \App\Livewire\Admin\Defense\MentorScoreInput::class)->name('mentor-score');
        Route::get('/{defense}/mentor-document/preview', [\App\Http\Controllers\MentorDocumentController::class, 'preview'])->name('mentor-document.preview');
        Route::get('/{defense}/mentor-document/download', [\App\Http\Controllers\MentorDocumentController::class, 'download'])->name('mentor-document.download');"""

content = content.replace("Route::get('/mentor-score', \App\Livewire\Admin\Defense\MentorScoreInput::class)->name('mentor-score');", replacement)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
