import sys

file_path = r'c:\Users\userJ\Documents\fortain\routes\web.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

routes_code = """
    // Defense Management - Internship
    Route::prefix('defenses/internship')->name('admin.defenses.internship.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Admin\Defense\Dashboard::class)->name('dashboard');
        Route::get('/participants', \App\Livewire\Admin\Defense\ParticipantManager::class)->name('participants');
        Route::get('/schedule', \App\Livewire\Admin\Defense\ScheduleManager::class)->name('schedule');
        Route::get('/mentor-score', \App\Livewire\Admin\Defense\MentorScoreInput::class)->name('mentor-score');
        Route::get('/recap', \App\Livewire\Admin\Defense\RecapAndDocuments::class)->name('recap');
    });
"""

if "defenses/internship" not in content:
    # insert before the end of admin group
    # We find the last `});` after admin
    admin_pos = content.find("Route::prefix('admin')")
    end_admin_group = content.find("});", admin_pos)
    if end_admin_group != -1:
        content = content[:end_admin_group] + routes_code + content[end_admin_group:]

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
