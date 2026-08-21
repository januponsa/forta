<?php

namespace App\Livewire\Admin;

use App\Models\ActivityType;
use App\Models\Form;
use App\Models\AcademicCalendar;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FormManager extends Component
{
    use WithPagination;

    #[Url]
    public $semesterFilter = '';

    #[Url]
    public $search = '';

    #[Url]
    public $statusFilter = '';

    #[Url]
    public $perPage = 10;

    public $isReordering = false;

    public $formId;
    public $title;
    public $description;
    public $activity_type_id;
    public $phase;
    public $semester;
    public $open_at;
    public $close_at;
    public $status = 'draft';
    public $isOpen = false;
    // Semester Management Variables
    public $isSemesterModalOpen = false;
    public $semesterId;
    public $sem_name;
    public $sem_type = 'Gasal';
    public $sem_academic_year;
    public $sem_start_date;
    public $sem_end_date;
    public $sem_is_active = true;
    public $sem_publication_status = 'published';


    public function mount()
    {
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSemesterFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->semesterFilter = '';
        $this->search = '';
        $this->statusFilter = '';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function enableReorderMode()
    {
        if (empty($this->semesterFilter)) {
            session()->flash('error', 'Pilih satu semester untuk mengatur urutan form.');
            return;
        }
        $this->isReordering = true;
    }

    public function disableReorderMode()
    {
        $this->isReordering = false;
    }

    public function moveUp($formId)
    {
        $this->moveForm($formId, 'up');
    }

    public function moveDown($formId)
    {
        $this->moveForm($formId, 'down');
    }

    private function moveForm($formId, $direction)
    {
        if (auth()->user()->role !== 'superadmin') {
            session()->flash('error', 'Anda tidak memiliki akses untuk mengubah urutan.');
            return;
        }

        $form = Form::findOrFail($formId);
        
        $operator = $direction === 'up' ? '<' : '>';
        $orderDirection = $direction === 'up' ? 'desc' : 'asc';

        $adjacent = Form::where('semester', $form->semester)
            ->where('display_order', $operator, $form->display_order)
            ->orderBy('display_order', $orderDirection)
            ->first();

        if ($adjacent) {
            $tempOrder = $form->display_order;
            $form->update(['display_order' => $adjacent->display_order]);
            $adjacent->update(['display_order' => $tempOrder]);
            session()->flash('message', "Urutan form berhasil diperbarui.");
        }
    }

    public function reorder(array $orderedIds)
    {
        if (auth()->user()->role !== 'superadmin') {
            session()->flash('error', 'Anda tidak memiliki akses untuk mengubah urutan.');
            $this->disableReorderMode();
            return;
        }

        if (empty($this->semesterFilter)) {
            session()->flash('error', 'Reorder hanya dapat dilakukan jika memilih satu semester.');
            $this->disableReorderMode();
            return;
        }

        if (count(array_unique($orderedIds)) !== count($orderedIds)) {
            session()->flash('error', 'ID Form tidak valid (duplikat).');
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($orderedIds as $index => $id) {
                $form = Form::where('id', $id)
                    ->where('semester', $this->semesterFilter)
                    ->first();
                    
                if ($form) {
                    $form->update(['display_order' => $index + 1]);
                } else {
                    throw new \Exception('Form tidak ditemukan pada semester ini.');
                }
            }
            DB::commit();
            session()->flash('message', 'Urutan form berhasil diperbarui.');
            $this->disableReorderMode();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal mengatur urutan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        if ($this->statusFilter === 'archived') {
            $query = Form::onlyTrashed()
                ->with(['activityType'])
                ->withCount('submissions');
        } else {
            $query = Form::query()
                ->with(['activityType'])
                ->withCount('submissions');
        }

        if ($this->semesterFilter) {
            $query->where('semester', $this->semesterFilter);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('form_code', 'like', "%{$search}%")
                  ->orWhere('phase', 'like', "%{$search}%")
                  ->orWhere('semester', 'like', "%{$search}%")
                  ->orWhereHas('activityType', function ($aq) use ($search) {
                      $aq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($this->semesterFilter) {
            $query->orderBy('display_order', 'asc');
        } else {
            $query->orderBy('semester', 'desc')->orderBy('display_order', 'asc');
        }
        $query->orderBy('created_at', 'desc');

        if ($this->isReordering || $this->perPage === 'all') {
            $forms = $query->get();
        } else {
            $forms = $query->paginate((int) $this->perPage);
        }

        $activityTypes = ActivityType::all();
        $semestersList = Form::select('semester')->distinct()->whereNotNull('semester')->pluck('semester');
        $calendars = AcademicCalendar::all();
        $allSemesters = collect($semestersList)->merge($calendars->pluck('semester_name'))->filter()->unique()->sortDesc()->values();

        return view('livewire.admin.form-manager', compact('forms', 'activityTypes', 'allSemesters', 'calendars'))
            ->layout('layouts.admin');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function resetInputFields()
    {
        $this->formId = null;
        $this->title = '';
        $this->description = '';
        $this->activity_type_id = '';
        $this->phase = '';
        $this->semester = '';
        $this->open_at = '';
        $this->close_at = '';
        $this->status = 'draft';
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'activity_type_id' => 'required|exists:activity_types,id',
            'phase' => 'required|string|max:255',
            'semester' => 'required|string|max:255',
            'open_at' => 'nullable|date',
            'close_at' => 'nullable|date|after_or_equal:open_at',
            'status' => 'required|in:draft,active,closed,archived',
        ]);

        if ($this->formId) {
            $form = Form::find($this->formId);
            $form->update([
                'title' => $this->title,
                'description' => $this->description,
                'activity_type_id' => $this->activity_type_id,
                'phase' => $this->phase,
                'semester' => $this->semester,
                'open_at' => $this->open_at ?: null,
                'close_at' => $this->close_at ?: null,
                'status' => $this->status,
            ]);
        } else {
            // new form placed at bottom
            $maxOrder = Form::where('semester', $this->semester)->max('display_order') ?? 0;

            Form::create([
                'title' => $this->title,
                'slug' => Str::slug($this->title).'-'.Str::random(6),
                'description' => $this->description,
                'activity_type_id' => $this->activity_type_id,
                'phase' => $this->phase,
                'semester' => $this->semester,
                'open_at' => $this->open_at ?: null,
                'close_at' => $this->close_at ?: null,
                'status' => $this->status,
                'display_order' => $maxOrder + 1,
            ]);
        }

        session()->flash('message', $this->formId ? 'Form berhasil diupdate.' : 'Form berhasil ditambahkan.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $form = Form::withTrashed()->findOrFail($id);
        $this->formId = $id;
        $this->title = $form->title;
        $this->description = $form->description;
        $this->activity_type_id = $form->activity_type_id;
        $this->phase = $form->phase;
        $this->semester = $form->semester;
        $this->open_at = $form->open_at ? $form->open_at->format('Y-m-d\TH:i') : '';
        $this->close_at = $form->close_at ? $form->close_at->format('Y-m-d\TH:i') : '';
        $this->status = $form->status;

        $this->openModal();
    }

    public function delete($id)
    {
        $form = Form::withCount('submissions')->findOrFail($id);
        if ($form->submissions_count > 0) {
            session()->flash('error', 'Form tidak dapat dihapus karena sudah memiliki submission mahasiswa.');
            return;
        }
        $form->fields()->delete();
        $form->sections()->delete();
        $form->delete();
        session()->flash('message', 'Form berhasil dihapus.');
    }

    public function activate($id)
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
        if (auth()->user()->role !== 'superadmin') {
            session()->flash('error', 'Hanya superadmin yang dapat menghapus form secara permanen.');
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


    public function toggleSemesterStatus($id)
    {
        $sem = AcademicCalendar::findOrFail($id);
        $sem->is_active = !$sem->is_active;
        $sem->save();
        session()->flash('semester_message', 'Status semester berhasil diubah.');
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
