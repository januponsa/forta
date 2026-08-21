import sys

file_path = r'c:\Users\userJ\Documents\fortain\app\Livewire\Admin\FormManager.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Add semester variables
sem_vars = """    // Semester Management Variables
    public $isSemesterModalOpen = false;
    public $semesterId;
    public $sem_name;
    public $sem_type = 'Gasal';
    public $sem_academic_year;
    public $sem_start_date;
    public $sem_end_date;
    public $sem_is_active = true;
    public $sem_publication_status = 'published';
"""
content = content.replace("public $isOpen = false;", "public $isOpen = false;\n" + sem_vars)

# Fix duplicate, activate, deletePermanently, createNewVersion and add Semester Management
# We will truncate the file from `public function activate($id)` to the end and replace it.
split_point = '    public function activate($id)'
before, after = content.split(split_point, 1)

new_methods = """    public function activate($id)
    {
        $form = Form::withTrashed()->withCount('fields')->findOrFail($id);
        if ($form->fields_count < 1) {
            session()->flash('error', 'Form tidak dapat diaktifkan karena belum memiliki pertanyaan/field (Minimal 1).');
            return;
        }
        $form->update(['status' => 'active']);
        session()->flash('message', 'Form berhasil diaktifkan.');
    }

    public function closeForm($id)
    {
        $form = Form::withTrashed()->findOrFail($id);
        $form->update(['status' => 'closed']);
        session()->flash('message', 'Form berhasil ditutup.');
    }

    public function archive($id)
    {
        $form = Form::findOrFail($id);
        $form->update(['status' => 'archived']);
        $form->delete(); // Soft delete
        session()->flash('message', 'Form berhasil diarsipkan.');
    }

    public function restore($id)
    {
        $form = Form::onlyTrashed()->findOrFail($id);
        $form->restore();
        $form->update(['status' => 'draft']);
        session()->flash('message', 'Form berhasil direstore sebagai draft.');
    }

    public function duplicate($id)
    {
        DB::beginTransaction();
        try {
            $form = Form::withTrashed()->with(['sections', 'fields'])->findOrFail($id);

            $newForm = $form->replicate();
            $newForm->deleted_at = null;
            $newForm->title = 'Salinan - ' . $form->title;
            $newForm->slug = Str::slug($newForm->title).'-'.Str::random(6);
            if ($form->form_code) {
                $newForm->form_code = $form->form_code . '_copy_' . Str::random(4);
            }
            $newForm->status = 'draft';
            $newForm->open_at = null;
            $newForm->close_at = null;
            $newForm->parent_form_id = null;
            $newForm->version = 1;
            
            $maxOrder = Form::where('semester', $form->semester)->max('display_order') ?? 0;
            $newForm->display_order = $maxOrder + 1;
            
            $newForm->save();

            $sectionMap = [];
            foreach ($form->sections as $section) {
                $newSection = $section->replicate();
                $newSection->form_id = $newForm->id;
                $newSection->save();
                $sectionMap[$section->id] = $newSection->id;
            }

            foreach ($form->fields as $field) {
                $newField = $field->replicate();
                $newField->form_id = $newForm->id;
                if ($field->section_id && isset($sectionMap[$field->section_id])) {
                    $newField->section_id = $sectionMap[$field->section_id];
                } else {
                    $newField->section_id = null;
                }
                $newField->save();
            }
            DB::commit();
            session()->flash('message', 'Form berhasil diduplikasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menduplikasi form: ' . $e->getMessage());
        }
    }

    public function deletePermanently($id)
    {
        if (auth()->user()->role !== 'admin_forta') {
            session()->flash('error', 'Hanya admin_forta yang dapat menghapus form secara permanen.');
            return;
        }
        $form = Form::onlyTrashed()->withCount('submissions')->findOrFail($id);
        if ($form->submissions_count > 0) {
            session()->flash('error', 'Form tidak dapat dihapus secara permanen karena memiliki data submission mahasiswa.');
            return;
        }
        DB::beginTransaction();
        try {
            $form->fields()->forceDelete();
            $form->sections()->forceDelete();
            $form->forceDelete();
            DB::commit();
            session()->flash('message', 'Form berhasil dihapus secara permanen.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menghapus form secara permanen: ' . $e->getMessage());
        }
    }

    public function createNewVersion($id)
    {
        DB::beginTransaction();
        try {
            $form = Form::with(['sections', 'fields'])->findOrFail($id);
            
            $maxVersion = Form::withTrashed()
                ->where('parent_form_id', $form->id)
                ->orWhere('id', $form->id)
                ->orWhere('parent_form_id', $form->parent_form_id)
                ->max('version') ?? $form->version;
                
            $newVersion = $maxVersion + 1;

            $cleanFormCode = preg_replace('/_v\d+$/', '', $form->form_code ?? '');

            // Rename the old/parent form code to avoid unique constraint violation
            if ($form->form_code === $cleanFormCode && $cleanFormCode !== '') {
                $form->update(['form_code' => $cleanFormCode . '_v' . $form->version]);
            }

            $newForm = $form->replicate();
            $newForm->deleted_at = null;
            $newForm->form_code = $cleanFormCode !== '' ? $cleanFormCode : null;
            $newForm->version = $newVersion;
            $newForm->parent_form_id = $form->parent_form_id ?? $form->id;
            $newForm->status = 'draft';
            $newForm->open_at = null;
            $newForm->close_at = null;
            $newForm->slug = Str::slug($form->title . '-v' . $newVersion) . '-' . Str::random(6);

            $maxOrder = Form::where('semester', $form->semester)->max('display_order') ?? 0;
            $newForm->display_order = $maxOrder + 1;
            $newForm->save();

            $sectionMap = [];
            foreach ($form->sections as $section) {
                $newSection = $section->replicate();
                $newSection->form_id = $newForm->id;
                $newSection->save();
                $sectionMap[$section->id] = $newSection->id;
            }

            foreach ($form->fields as $field) {
                $newField = $field->replicate();
                $newField->form_id = $newForm->id;
                if ($field->section_id && isset($sectionMap[$field->section_id])) {
                    $newField->section_id = $sectionMap[$field->section_id];
                } else {
                    $newField->section_id = null;
                }
                $newField->save();
            }
            DB::commit();
            session()->flash('message', "Versi baru (v{$newVersion}) dari form '{$form->title}' berhasil dibuat sebagai draft.");
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal membuat versi baru: ' . $e->getMessage());
        }
    }

    // SEMESTER MANAGEMENT METHODS
    public function openSemesterModal()
    {
        $this->resetSemesterFields();
        $this->isSemesterModalOpen = true;
    }

    public function closeSemesterModal()
    {
        $this->isSemesterModalOpen = false;
    }

    public function resetSemesterFields()
    {
        $this->semesterId = null;
        $this->sem_name = '';
        $this->sem_type = 'Gasal';
        $this->sem_academic_year = '';
        $this->sem_start_date = '';
        $this->sem_end_date = '';
        $this->sem_is_active = true;
        $this->sem_publication_status = 'published';
    }

    public function saveSemester()
    {
        $this->validate([
            'sem_name' => 'required|string|max:255',
            'sem_type' => 'required|string',
            'sem_academic_year' => 'required|string|max:20',
            'sem_start_date' => 'required|date',
            'sem_end_date' => 'required|date|after_or_equal:sem_start_date',
        ]);

        if ($this->semesterId) {
            $sem = AcademicCalendar::find($this->semesterId);
            $sem->update([
                'semester_name' => $this->sem_name,
                'semester_type' => $this->sem_type,
                'academic_year' => $this->sem_academic_year,
                'start_date' => $this->sem_start_date,
                'end_date' => $this->sem_end_date,
                'is_active' => $this->sem_is_active,
                'publication_status' => $this->sem_publication_status,
            ]);
            session()->flash('message', 'Semester berhasil diupdate.');
        } else {
            AcademicCalendar::create([
                'semester_code' => Str::slug($this->sem_name) . '-' . rand(100, 999),
                'semester_name' => $this->sem_name,
                'semester_type' => $this->sem_type,
                'academic_year' => $this->sem_academic_year,
                'start_date' => $this->sem_start_date,
                'end_date' => $this->sem_end_date,
                'is_active' => $this->sem_is_active,
                'publication_status' => $this->sem_publication_status,
            ]);
            session()->flash('message', 'Semester berhasil ditambahkan.');
        }
        $this->closeSemesterModal();
    }

    public function editSemester($id)
    {
        $sem = AcademicCalendar::findOrFail($id);
        $this->semesterId = $sem->id;
        $this->sem_name = $sem->semester_name;
        $this->sem_type = $sem->semester_type;
        $this->sem_academic_year = $sem->academic_year;
        $this->sem_start_date = $sem->start_date ? $sem->start_date->format('Y-m-d') : '';
        $this->sem_end_date = $sem->end_date ? $sem->end_date->format('Y-m-d') : '';
        $this->sem_is_active = $sem->is_active;
        $this->sem_publication_status = $sem->publication_status;
        $this->isSemesterModalOpen = true;
    }

    public function deleteSemester($id)
    {
        $sem = AcademicCalendar::findOrFail($id);
        $formCount = Form::where('semester', $sem->semester_name)->count();
        if ($formCount > 0) {
            session()->flash('error', "Semester tidak dapat dihapus karena masih ada {$formCount} form yang terhubung. Silakan pindahkan/hapus form terlebih dahulu.");
            return;
        }
        $sem->delete();
        session()->flash('message', 'Semester berhasil dihapus.');
    }
}
"""

content = before + new_methods

# Render method update
content = content.replace(
    "$calendars = AcademicCalendar::select('semester_name')->distinct()->pluck('semester_name');",
    "$calendars = AcademicCalendar::all();"
).replace(
    "$allSemesters = collect($semestersList)->merge($calendars)->filter()->unique()->sortDesc()->values();",
    "$allSemesters = collect($semestersList)->merge($calendars->pluck('semester_name'))->filter()->unique()->sortDesc()->values();"
).replace(
    "compact('forms', 'activityTypes', 'allSemesters')",
    "compact('forms', 'activityTypes', 'allSemesters', 'calendars')"
)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
