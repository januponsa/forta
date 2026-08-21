import sys

file_path = r'c:\Users\userJ\Documents\fortain\app\Livewire\Admin\Defense\MentorScoreInput.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

replacement = "$this->documentUrl = $mentorDoc ? route('admin.submissions.file', ['file' => $mentorDoc->id]) : null;"
old_line = "$this->documentUrl = $mentorDoc ? '/storage/' . $mentorDoc->stored_path : null;"

content = content.replace(old_line, replacement)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
