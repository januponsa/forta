import sys
import re

file_path = r'c:\Users\userJ\Documents\fortain\app\Livewire\Admin\FormManager.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

toggle_method = """
    public function toggleSemesterStatus($id)
    {
        $sem = AcademicCalendar::findOrFail($id);
        $sem->is_active = !$sem->is_active;
        $sem->save();
        session()->flash('semester_message', 'Status semester berhasil diubah.');
    }

    public function saveSemester()"""

content = content.replace('    public function saveSemester()', toggle_method)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
