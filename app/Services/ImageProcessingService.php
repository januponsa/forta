<?php

namespace App\Services;

use App\Models\DocumentAsset;
use App\Models\DocumentAssetVersion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ImageProcessingService
{
    /**
     * Store a new image asset.
     */
    public function storeAsset(UploadedFile $file, string $type, string $name, $ownerType = 'system', $ownerId = null): DocumentAsset
    {
        $path = $file->store('document-assets/' . $type . 's', 'public');
        
        $imageInfo = @getimagesize($file->getRealPath());
        $width = $imageInfo[0] ?? 400;
        $height = $imageInfo[1] ?? 400;
        $aspectRatio = $height > 0 ? round($width / $height, 6) : 1;
        $mime = $file->getClientMimeType();

        $asset = DocumentAsset::create([
            'name' => $name,
            'asset_type' => $type,
            'mime_type' => $mime,
            'status' => 'active',
            'owner_type' => $ownerType,
            'owner_id' => $ownerId,
            'default_width' => min($width, 400),
            'default_height' => min($height, 400),
            'created_by' => Auth::id(),
        ]);

        $assetVersion = DocumentAssetVersion::create([
            'document_asset_id' => $asset->id,
            'version_number' => 1,
            'original_path' => $path,
            'original_width' => $width,
            'original_height' => $height,
            'aspect_ratio' => $aspectRatio,
            'file_size' => $file->getSize(),
            'file_format' => $file->getClientOriginalExtension(),
            'has_transparency' => in_array($mime, ['image/png', 'image/webp', 'image/svg+xml']),
            'created_by' => Auth::id(),
        ]);

        $asset->update(['active_version_id' => $assetVersion->id]);

        return $asset;
    }

    /**
     * Create a new version of an asset with crop/resize data.
     */
    public function createProcessedVersion(DocumentAsset $asset, array $processingData): DocumentAssetVersion
    {
        $oldVersion = $asset->activeVersion;
        $newVersionNumber = ($asset->versions()->max('version_number') ?? 0) + 1;

        // Save processing metadata (in a real system, we'd process the image using Intervention Image)
        // Here we just save the instructions (crop_data, rotation, processed_width) for the frontend to apply visually,
        // or for future backend rendering.

        $newVersion = DocumentAssetVersion::create([
            'document_asset_id' => $asset->id,
            'version_number' => $newVersionNumber,
            'original_path' => $oldVersion->original_path, // Keep the same original file
            'original_width' => $oldVersion->original_width,
            'original_height' => $oldVersion->original_height,
            'aspect_ratio' => $oldVersion->aspect_ratio,
            'file_size' => $oldVersion->file_size,
            'file_format' => $oldVersion->file_format,
            'has_transparency' => $oldVersion->has_transparency,
            
            // Processing data
            'crop_data' => $processingData['crop_data'] ?? $oldVersion->crop_data,
            'rotation' => $processingData['rotation'] ?? $oldVersion->rotation,
            'flip_horizontal' => $processingData['flip_horizontal'] ?? $oldVersion->flip_horizontal,
            'flip_vertical' => $processingData['flip_vertical'] ?? $oldVersion->flip_vertical,
            'opacity' => $processingData['opacity'] ?? $oldVersion->opacity,
            'object_fit' => $processingData['object_fit'] ?? $oldVersion->object_fit,
            'processed_width' => $processingData['processed_width'] ?? $oldVersion->processed_width,
            'processed_height' => $processingData['processed_height'] ?? $oldVersion->processed_height,
            
            'change_notes' => $processingData['change_notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        $asset->update(['active_version_id' => $newVersion->id]);

        // If default dimensions are provided, update the master asset
        if (isset($processingData['default_width']) && isset($processingData['default_height'])) {
            $asset->update([
                'default_width' => $processingData['default_width'],
                'default_height' => $processingData['default_height'],
            ]);
        }

        return $newVersion;
    }
}
