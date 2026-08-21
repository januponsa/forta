import os

files = [
    r'c:\Users\userJ\Documents\fortain\app\Livewire\Admin\Defense\ScheduleManager.php',
    r'c:\Users\userJ\Documents\fortain\app\Livewire\Admin\Defense\MentorScoreInput.php',
    r'c:\Users\userJ\Documents\fortain\app\Livewire\Admin\Defense\RecapAndDocuments.php',
]

for file_path in files:
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    if "->layout('layouts.admin')" not in content:
        content = content.replace("        ]);\n    }\n}", "        ])->layout('layouts.admin');\n    }\n}")
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)
