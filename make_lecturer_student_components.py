import os

base_path = r'c:\Users\userJ\Documents\fortain'

components = {
    'Lecturer': {
        "MyDefenses": "my-defenses",
        "AssessmentForm": "assessment-form",
        "SuggestionForm": "suggestion-form",
    },
    'Student': {
        "DefenseStatus": "status",
        "RevisionManager": "revision-manager",
        "FinalResult": "final-result",
    }
}

for role, comps in components.items():
    for class_name, view_name in comps.items():
        # Write class file
        class_path = os.path.join(base_path, 'app', 'Livewire', role, 'Defense', f'{class_name}.php')
        os.makedirs(os.path.dirname(class_path), exist_ok=True)
        with open(class_path, 'w', encoding='utf-8') as f:
            f.write(f"""<?php

namespace App\Livewire\{role}\Defense;

use Livewire\Component;

class {class_name} extends Component
{{
    public function render()
    {{
        return view('livewire.{role.lower()}.defense.{view_name}');
    }}
}}
""")

        # Write view file
        view_path = os.path.join(base_path, 'resources', 'views', 'livewire', role.lower(), 'defense', f'{view_name}.blade.php')
        os.makedirs(os.path.dirname(view_path), exist_ok=True)
        with open(view_path, 'w', encoding='utf-8') as f:
            f.write(f"""<div>
    @section('title', 'Sidang Magang - {class_name}')
    
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

print("Lecturer and Student components created successfully.")
