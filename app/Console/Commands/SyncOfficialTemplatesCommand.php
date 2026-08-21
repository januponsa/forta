<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncOfficialTemplatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forta:forms:sync-official-templates {--dry-run} {--apply}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Idempotently sync official FORTA form templates';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\OfficialFormTemplateService $service)
    {
        if (!$this->option('apply') && !$this->option('dry-run')) {
            $this->error('Please specify --dry-run or --apply mode.');
            return;
        }

        $dryRun = $this->option('dry-run');
        $this->info("Starting template sync... Mode: " . ($dryRun ? 'DRY-RUN' : 'APPLY'));

        $templates = $service->getTemplates();

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            foreach ($templates as $t) {
                $this->syncTemplate($t);
            }

            // Sync dependencies after all forms are created
            foreach ($templates as $t) {
                if (!empty($t['depends_on_form_code'])) {
                    $parent = \App\Models\Form::where('form_code', $t['depends_on_form_code'])->first();
                    $child = \App\Models\Form::where('form_code', $t['form_code'])->first();
                    if ($parent && $child) {
                        $child->update(['depends_on_form_id' => $parent->id]);
                        $this->line("Linked {$child->form_code} to depend on {$parent->form_code}");
                    }
                }
            }

            if ($dryRun) {
                \Illuminate\Support\Facades\DB::rollBack();
                $this->info('DRY RUN: All changes rolled back successfully.');
            } else {
                \Illuminate\Support\Facades\DB::commit();
                $this->info('APPLY: All changes committed successfully.');
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            $this->error("Sync failed: " . $e->getMessage());
        }
    }

    protected function syncTemplate($t)
    {
        // 1. Sync Activity Type
        $activityType = \App\Models\ActivityType::firstOrCreate(
            ['slug' => $t['activity_type_slug']],
            ['name' => $t['jenis_kegiatan']]
        );

        // 2. Sync Form
        $form = \App\Models\Form::firstOrNew(['form_code' => $t['form_code']]);
        $form->title = $t['nama'];
        $form->description = $t['description'] ?? '';
        $form->slug = $form->slug ?: \Illuminate\Support\Str::slug($t['nama'] . '-' . uniqid());
        $form->activity_type_id = $activityType->id;
        $form->phase = $t['fase'];
        $form->semester = $t['semester'] ?? 'Ganjil';
        $form->status = $t['status'] ?? 'active';
        $form->save();

        $this->info("Synced Form: {$form->form_code}");

        // 3. Sync Sections & Fields
        foreach ($t['sections'] as $sIndex => $sec) {
            $section = \App\Models\FormSection::firstOrNew([
                'form_id' => $form->id,
                'section_code' => $sec['section_code'],
            ]);
            $section->title = $sec['title'];
            $section->description = $sec['description'] ?? null;
            $section->order = $sec['order'] ?? ($sIndex + 1);
            $section->save();

            if (isset($sec['fields'])) {
                foreach ($sec['fields'] as $fIndex => $fld) {
                    $field = \App\Models\FormField::firstOrNew([
                        'form_id' => $form->id,
                        'name' => $fld['name'],
                    ]);
                    $field->section_id = $section->id;
                    $field->label = $fld['label'];
                    $field->type = $fld['type'];
                    $field->is_required = $fld['is_required'] ?? false;
                    $field->order = $fld['order'] ?? ($fIndex + 1);
                    $field->options = $fld['options'] ?? null;
                    $field->conditions = $fld['conditions'] ?? null;
                    $field->max_files = $fld['max_files'] ?? null;
                    $field->max_size_mb = $fld['max_size_mb'] ?? null;
                    $field->allowed_types = $fld['allowed_types'] ?? null;
                    $field->save();
                }
            }
        }
    }
}
