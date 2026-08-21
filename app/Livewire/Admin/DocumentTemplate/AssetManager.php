<?php

namespace App\Livewire\Admin\DocumentTemplate;

use App\Models\DocumentAsset;
use App\Services\ImageProcessingService;
use Livewire\Component;
use Livewire\WithFileUploads;

class AssetManager extends Component
{
    use WithFileUploads;

    public $assets;
    public $typeFilter = '';
    public $search = '';
    
    public $showUploadForm = false;
    public $newAsset;
    public $newName = '';
    public $newType = 'logo';

    public $showEditorModal = false;
    public $editingAssetId;
    public $editorData = [
        'width' => 100,
        'height' => 100,
        'rotation' => 0,
        'object_fit' => 'contain',
    ];

    public function loadAssets()
    {
        $query = DocumentAsset::with(['activeVersion', 'creator'])
            ->where('owner_type', 'system');

        if ($this->typeFilter) {
            $query->where('asset_type', $this->typeFilter);
        }
        
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $this->assets = $query->orderBy('created_at', 'desc')->get();
    }

    public function mount()
    {
        $this->loadAssets();
    }

    public function updatedTypeFilter()
    {
        $this->loadAssets();
    }
    
    public function updatedSearch()
    {
        $this->loadAssets();
    }

    public function uploadAsset(ImageProcessingService $service)
    {
        $this->validate([
            'newAsset' => 'required|image|mimes:png,jpg,jpeg,webp,svg|max:2048',
            'newName' => 'required|string|max:255',
            'newType' => 'required|in:logo,stamp,image,photo',
        ]);

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($this->newAsset->getRealPath());
        $allowedMimes = ['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'];
        
        if (!in_array($realMime, $allowedMimes)) {
            session()->flash('error', 'File yang diunggah bukan gambar valid (MIME: ' . $realMime . ').');
            return;
        }

        $service->storeAsset($this->newAsset, $this->newType, $this->newName);

        session()->flash('message', 'Aset berhasil diunggah.');
        $this->showUploadForm = false;
        $this->reset(['newAsset', 'newName', 'newType']);
        $this->loadAssets();
    }

    public function editAsset($id)
    {
        $asset = DocumentAsset::with('activeVersion')->findOrFail($id);
        $this->editingAssetId = $asset->id;
        
        $this->editorData = [
            'width' => $asset->default_width,
            'height' => $asset->default_height,
            'rotation' => $asset->activeVersion->rotation ?? 0,
            'object_fit' => $asset->activeVersion->object_fit ?? 'contain',
        ];
        
        $this->showEditorModal = true;
    }

    public function saveEdits(ImageProcessingService $service)
    {
        $asset = DocumentAsset::findOrFail($this->editingAssetId);
        
        $service->createProcessedVersion($asset, [
            'default_width' => $this->editorData['width'],
            'default_height' => $this->editorData['height'],
            'rotation' => $this->editorData['rotation'],
            'object_fit' => $this->editorData['object_fit'],
            'change_notes' => 'Diubah via editor',
        ]);

        session()->flash('message', 'Perubahan ukuran aset berhasil disimpan.');
        $this->showEditorModal = false;
        $this->loadAssets();
    }

    public function deleteAsset($id)
    {
        $asset = DocumentAsset::findOrFail($id);
        $asset->delete();
        session()->flash('message', 'Aset berhasil dihapus.');
        $this->loadAssets();
    }

    public function render()
    {
        return view('livewire.admin.document-template.asset-manager')->layout('layouts.admin');
    }
}
