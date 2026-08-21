<div class="bg-slate-50 hover:bg-slate-100 rounded-xl border border-slate-200 p-4 transition-colors" data-id="{{ $field->id }}">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        
        <!-- Field Info -->
        <div class="flex items-start space-x-3">
            <!-- Drag Handle / Up-Down Order Controls -->
            <div class="flex flex-col items-center justify-center space-y-1 bg-white border border-slate-200 p-1.5 rounded-lg shrink-0">
                <span class="field-drag-handle cursor-move text-slate-400 hover:text-slate-600 mb-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 2zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 14zm6-8a2 2 0 1 0-.001-3.999A2 2 0 0 0 13 6zm0 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 14z"></path></svg>
                </span>
                <button wire:click="moveFieldUp({{ $field->id }})" title="Naikkan" class="text-slate-400 hover:text-slate-700 text-xs leading-none font-bold select-none">&uarr;</button>
                <span class="text-[10px] font-bold text-slate-500 leading-none">{{ $field->order }}</span>
                <button wire:click="moveFieldDown({{ $field->id }})" title="Turunkan" class="text-slate-400 hover:text-slate-700 text-xs leading-none font-bold select-none">&darr;</button>
            </div>
            
            <div class="space-y-1">
                <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                    <span class="text-xs font-bold text-slate-800">{{ $field->label }}</span>
                    @if($field->is_required)
                        <span class="bg-red-50 text-red-700 text-[10px] font-bold px-1.5 py-0.5 rounded">Wajib</span>
                    @endif
                    @if(!$field->is_active)
                        <span class="bg-slate-200 text-slate-700 text-[10px] font-bold px-1.5 py-0.5 rounded">Nonaktif</span>
                    @endif
                    <span class="bg-blue-50 text-blue-700 text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase">{{ $field->type }}</span>
                </div>
                
                @if($field->description)
                    <p class="text-[11px] text-slate-500">{{ $field->description }}</p>
                @endif
                
                @if($field->placeholder)
                    <p class="text-[10px] text-slate-400 italic">Placeholder: "{{ $field->placeholder }}"</p>
                @endif

                @if(is_array($field->options) && count($field->options) > 0)
                    <p class="text-[10px] text-slate-600">Opsi: <span class="font-medium">{{ implode(', ', $field->options) }}</span></p>
                @endif

                @if($field->type === 'file')
                    <p class="text-[10px] text-slate-500">Allowed: {{ is_array($field->allowed_types) ? implode(', ', $field->allowed_types) : 'Any' }} | Max: {{ $field->max_files }} files ({{ $field->max_size_mb }}MB)</p>
                @endif

                <!-- Conditional Visibility Logic Indicator -->
                @if(is_array($field->conditions) && isset($field->conditions['trigger_field_id']))
                    @php
                        $triggerField = \App\Models\FormField::find($field->conditions['trigger_field_id']);
                    @endphp
                    @if($triggerField)
                        <div class="mt-1 bg-indigo-50 text-indigo-800 text-[10px] px-2 py-1 rounded border border-indigo-100 inline-block">
                            Syarat Tampil: Jika <strong>"{{ $triggerField->label }}"</strong> {{ $field->conditions['operator'] }} <strong>"{{ $field->conditions['value'] }}"</strong>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Field Actions -->
        <div class="flex items-center space-x-2 self-end md:self-center">
            
            <!-- Save to Bank Category selection -->
            <div class="flex items-center space-x-1 border border-slate-200 bg-white px-2 py-1 rounded-lg text-xs" x-data="{ open: false }">
                <select wire:model="saveToBankCategory" class="text-[10px] bg-transparent border-0 p-0 focus:ring-0">
                    <option value="Identitas">Identitas</option>
                    <option value="Kegiatan">Kegiatan</option>
                    <option value="Tanggal">Tanggal</option>
                    <option value="Instansi">Instansi</option>
                    <option value="Akademik">Akademik</option>
                    <option value="Dokumen">Dokumen</option>
                    <option value="Evaluasi">Evaluasi</option>
                    <option value="Pelaporan">Pelaporan</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
                <button type="button" wire:click="saveToBank({{ $field->id }})" title="Simpan ke Bank Pertanyaan" class="text-indigo-600 hover:text-indigo-800 font-bold ml-1">
                    Simpan ke Bank
                </button>
            </div>

            <button wire:click="openFieldModal({{ $field->id }}, {{ $field->section_id }})" class="bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-semibold px-2.5 py-1 rounded text-xs transition-colors">
                Edit
            </button>
            <button wire:click="duplicateField({{ $field->id }})" class="bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-semibold px-2.5 py-1 rounded text-xs transition-colors">
                Salin
            </button>
            <button wire:click="deleteField({{ $field->id }})" onclick="confirm('Hapus pertanyaan ini?') || event.stopImmediatePropagation()" class="bg-red-50 text-red-700 hover:bg-red-100 font-semibold px-2.5 py-1 rounded text-xs transition-colors">
                Hapus
            </button>
        </div>

    </div>
</div>
