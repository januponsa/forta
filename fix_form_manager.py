import sys
import re

file_path = r'c:\Users\userJ\Documents\fortain\app\Livewire\Admin\FormManager.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# We need to remove the first `public function duplicate($id)` block
# The first one is from line ~330, the second one is at ~412.
# Actually let's just find the first one and delete it.
pattern = r'    public function duplicate\(\$id\)\s*\{\s*\$form = Form::withTrashed\(\)->with\(\[\'sections\', \'fields\'\]\)->findOrFail\(\$id\);\s*\$newForm = \$form->replicate\(\);.*?session\(\)->flash\(\'message\', \'Form berhasil diduplikasi\.\'\);\s*\}'

# The regex might be tricky. Let's just find the indices of "public function duplicate($id)"
indices = [m.start() for m in re.finditer(r'public function duplicate\(\$id\)', content)]

if len(indices) > 1:
    # There are duplicates! Let's remove the first one.
    start_idx = indices[0]
    # find the end of this method. Next method is `public function activate($id)`
    next_method_idx = content.find('public function activate($id)', start_idx)
    
    if next_method_idx != -1:
        # Before the first duplicate
        before = content[:start_idx]
        # From `activate` onwards
        after = content[next_method_idx:]
        
        content = before + after

        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)
        print("Fixed duplicates!")
    else:
        print("Couldn't find next method.")
else:
    print("No duplicates found.")

