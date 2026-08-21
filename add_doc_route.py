import sys
import re

file_path = r'c:\Users\userJ\Documents\fortain\routes\web.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

replacement = """        Route::get('/{defense}/mentor-document/download', [\App\Http\Controllers\MentorDocumentController::class, 'download'])->name('mentor-document.download');
        
        // Generated Documents Download
        Route::get('/documents/{id}/download', [\App\Http\Controllers\GeneratedDocumentController::class, 'download'])->name('documents.download');"""

content = content.replace("        Route::get('/{defense}/mentor-document/download', [\App\Http\Controllers\MentorDocumentController::class, 'download'])->name('mentor-document.download');", replacement)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
