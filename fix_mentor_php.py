import sys
import re

file_path = r'c:\Users\userJ\Documents\fortain\app\Livewire\Admin\Defense\MentorScoreInput.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

replacement = """        $this->caseId = $case->id;
        $this->studentName = $case->student->name ?? 'Unknown';
        
        // Dispatch event for PDF viewer
        $this->dispatch(
            'open-mentor-pdf-viewer',
            previewUrl: route('admin.defenses.internship.mentor-document.preview', $this->caseId),
            downloadUrl: route('admin.defenses.internship.mentor-document.download', $this->caseId)
        );"""

pattern = re.compile(r'\s*\$this->caseId = \$case->id;\s*\$this->studentName = \$case->student->name \?\? \'Unknown\';\s*// Try to find mentor document.*?\$this->documentUrl = \$mentorDoc \? route\(\'admin\.submissions\.file\', \[\'file\' => \$mentorDoc->id\]\) : null;', re.DOTALL)
content = pattern.sub('\n' + replacement, content)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
