<?php

namespace App\Livewire\Admin;

use App\Models\ActivityType;
use Illuminate\Support\Str;
use Livewire\Component;

class ActivityTypeManager extends Component
{
    public $typeId;

    public $name;

    public $isOpen = false;

    public function render()
    {
        $activityTypes = ActivityType::all();

        return view('livewire.admin.activity-type-manager', compact('activityTypes'))
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
        $this->name = '';
        $this->typeId = null;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:activity_types,name,'.$this->typeId,
        ]);

        if ($this->typeId) {
            $type = ActivityType::find($this->typeId);
            $type->update([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
            ]);
        } else {
            ActivityType::create([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
            ]);
        }

        session()->flash('message',
            $this->typeId ? 'Jenis kegiatan berhasil diupdate.' : 'Jenis kegiatan berhasil ditambahkan.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $type = ActivityType::findOrFail($id);
        $this->typeId = $id;
        $this->name = $type->name;

        $this->openModal();
    }

    public function delete($id)
    {
        $type = ActivityType::findOrFail($id);
        if ($type->forms()->exists()) {
            session()->flash('error', 'Jenis kegiatan tidak dapat dihapus karena masih digunakan pada Form Akademik.');
            return;
        }
        $type->delete();
        session()->flash('message', 'Jenis kegiatan berhasil dihapus.');
    }
}
