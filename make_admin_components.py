import os

base_path = r'c:\Users\userJ\Documents\fortain'

components = {
    "Dashboard": "dashboard",
    "ParticipantManager": "participant-manager",
    "ScheduleManager": "schedule-manager",
    "MentorScoreInput": "mentor-score-input",
    "RecapAndDocuments": "recap-and-documents",
}

for class_name, view_name in components.items():
    # Write class file
    class_path = os.path.join(base_path, 'app', 'Livewire', 'Admin', 'Defense', f'{class_name}.php')
    os.makedirs(os.path.dirname(class_path), exist_ok=True)
    with open(class_path, 'w', encoding='utf-8') as f:
        f.write(f"""<?php

namespace App\Livewire\Admin\Defense;

use Livewire\Component;

class {class_name} extends Component
{{
    public function render()
    {{
        return view('livewire.admin.defense.{view_name}');
    }}
}}
""")

    # Write view file
    view_path = os.path.join(base_path, 'resources', 'views', 'livewire', 'admin', 'defense', f'{view_name}.blade.php')
    os.makedirs(os.path.dirname(view_path), exist_ok=True)
    with open(view_path, 'w', encoding='utf-8') as f:
        f.write(f"""<div>
    @section('title', 'Manajemen Sidang - {class_name}')
    
    <div class="px-4 py-5 sm:px-6 bg-white border-b border-gray-200">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            {class_name}
        </h3>
    </div>
    <div class="p-4">
        {class_name} Content
    </div>
</div>
""")

print("Admin components created successfully.")
