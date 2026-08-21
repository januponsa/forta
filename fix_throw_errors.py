import sys

file_path = r'c:\Users\userJ\Documents\fortain\app\Livewire\Student\StudentFormFiller.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

replacement = """        if (count($rules) > 0) {
            $this->validate($rules, $messages);
        }

        if ($this->getErrorBag()->isNotEmpty()) {
            throw \Illuminate\Validation\ValidationException::withMessages($this->getErrorBag()->toArray());
        }
    }"""

old_block = """        if (count($rules) > 0) {
            $this->validate($rules, $messages);
        }
    }"""

content = content.replace(old_block, replacement)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
