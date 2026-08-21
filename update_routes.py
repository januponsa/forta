import sys
import re

with open(r'c:\Users\userJ\Documents\fortain\routes\web.php', 'r', encoding='utf-8') as f:
    content = f.read()

route_str = """    Route::get('/email-blast', \App\Livewire\Admin\EmailBlastManager::class)->name('admin.email-blast');
    Route::get('/email-blast/history', \App\Livewire\Admin\EmailBlastHistory::class)->name('admin.email-blast.history');
    Route::get('/email-blast/history/{id}', \App\Livewire\Admin\EmailBlastDetail::class)->name('admin.email-blast.detail');"""

# Insert before end of admin route group
if 'Route::get(\'/email-blast\'' not in content:
    content = content.replace("    Route::get('/settings/master-data', \App\Livewire\Admin\MasterData::class)->name('admin.settings.master-data');", 
    "    Route::get('/settings/master-data', \App\Livewire\Admin\MasterData::class)->name('admin.settings.master-data');\n\n" + route_str)

with open(r'c:\Users\userJ\Documents\fortain\routes\web.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Routes updated")
