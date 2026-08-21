<?php

namespace App\Services;

use App\Models\DocumentTemplate;
use App\Models\DocumentTemplateVersion;
use App\Models\DocumentInstance;
use App\Models\DocumentInstanceOverride;
use Illuminate\Support\Facades\Auth;
use Spatie\Browsershot\Browsershot;

class DocumentBuilderService
{
    /**
     * Get the active template version for a specific purpose.
     */
    public function getActiveTemplate(string $purpose): ?DocumentTemplateVersion
    {
        $template = DocumentTemplate::where('document_purpose', $purpose)
            ->where('status', 'published')
            ->first();

        return $template?->activeVersion;
    }

    /**
     * Generate a new document instance (snapshot) from a template and data source.
     */
    public function generateInstance(DocumentTemplateVersion $templateVersion, $sourceModel, array $data = []): DocumentInstance
    {
        // Cancel existing non-final instances for this source and template
        DocumentInstance::where('source_type', get_class($sourceModel))
            ->where('source_id', $sourceModel->id)
            ->where('document_template_id', $templateVersion->document_template_id)
            ->whereNotIn('status', ['final', 'cancelled', 'revised'])
            ->update(['status' => 'cancelled']);

        return DocumentInstance::create([
            'document_template_id' => $templateVersion->document_template_id,
            'template_version_id' => $templateVersion->id,
            'letterhead_version_id' => $templateVersion->letterhead_version_id,
            'source_type' => get_class($sourceModel),
            'source_id' => $sourceModel->id,
            'status' => 'draft',
            'data_snapshot' => $data,
            'asset_snapshots' => [], // Add needed asset metadata here
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    }

    /**
     * Set a manual override for a specific field in a document instance.
     */
    public function setOverride(DocumentInstance $instance, string $fieldKey, $overrideValue, string $overrideType = 'text', string $reason = null)
    {
        DocumentInstanceOverride::updateOrCreate(
            ['document_instance_id' => $instance->id, 'field_key' => $fieldKey],
            [
                'override_value' => $overrideValue,
                'override_type' => $overrideType,
                'reason' => $reason,
                'overridden_by' => Auth::id(),
            ]
        );

        // Update the cached override data on the instance
        $overrides = $instance->overrides()->pluck('override_value', 'field_key')->toArray();
        $instance->update(['override_data' => $overrides, 'updated_by' => Auth::id()]);
    }

    /**
     * Finalize the document instance (locks it from further edits).
     */
    public function finalizeInstance(DocumentInstance $instance): void
    {
        $instance->update([
            'status' => 'final',
            'finalized_at' => now(),
            'finalized_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    }

    /**
     * Resolve placeholders in HTML using data and overrides.
     */
    public function resolvePlaceholders(string $html, array $data, array $overrides = []): string
    {
        $resolvedHtml = $html;

        // Apply manual overrides first (if any placeholder has an exact override key)
        foreach ($overrides as $key => $value) {
            if (is_string($value)) {
                $resolvedHtml = str_replace($key, $value, $resolvedHtml);
            }
        }

        // Apply data mapping
        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $resolvedHtml = str_replace('{{ ' . $key . ' }}', $value, $resolvedHtml);
            }
        }

        // Clean up unmapped placeholders (optional, maybe leave them for debugging)
        // $resolvedHtml = preg_replace('/\{\{.*?\}\}/', '', $resolvedHtml);

        return $resolvedHtml;
    }

    /**
     * Render HTML view for the document (includes letterhead).
     */
    public function renderHtml(DocumentInstance $instance): string
    {
        $templateVersion = $instance->templateVersion;
        $letterheadVersion = $instance->letterheadVersion;
        $data = $instance->data_snapshot ?? [];
        $overrides = $instance->override_data ?? [];

        $bodyHtml = $this->resolvePlaceholders($templateVersion->body_html ?? '', $data, $overrides);

        return view('pdf.document-builder-layout', [
            'template' => $templateVersion,
            'letterhead' => $letterheadVersion,
            'bodyHtml' => $bodyHtml,
        ])->render();
    }

    /**
     * Generate final PDF file using Browsershot.
     */
    public function generatePdf(DocumentInstance $instance, string $outputPath): bool
    {
        $html = $this->renderHtml($instance);
        $template = $instance->templateVersion;

        $browsershot = Browsershot::html($html)
            ->format($template->paper_size ?? 'A4')
            ->margins(
                $template->margin_top ?? 25,
                $template->margin_right ?? 25,
                $template->margin_bottom ?? 25,
                $template->margin_left ?? 25
            )
            ->showBackground();

        if ($template->orientation === 'landscape') {
            $browsershot->landscape();
        }

        $browsershot->save($outputPath);

        $instance->update([
            'file_path' => $outputPath,
            'file_checksum' => md5_file($outputPath),
        ]);

        return true;
    }
}
