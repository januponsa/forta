import sys

file_path = r'c:\Users\userJ\Documents\fortain\app\Livewire\Admin\Defense\MentorScoreInput.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

replacement = """        // Try to find mentor document
        $mentorDoc = null;
        if ($case->submission) {
            $mentorDoc = \\App\\Models\\SubmissionFile::where('submission_id', $case->submission_id)
                ->whereHas('field', function($q) {
                    $q->where('name', 'INTDEF_mentor_evaluation_file');
                })->first();
        }
        $this->documentUrl = $mentorDoc ? '/storage/' . $mentorDoc->stored_path : null;"""

# Using simple string slice instead of regex to avoid escape sequence issues
start_str = "// Try to find mentor document"
end_str = "$this->documentUrl = $mentorDoc ? '/storage/' . $mentorDoc['path'] : null;"

start_idx = content.find(start_str)
end_idx = content.find(end_str) + len(end_str)

if start_idx != -1 and end_idx != -1:
    new_content = content[:start_idx] + replacement + content[end_idx:]
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(new_content)
