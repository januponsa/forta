<div>
    @section('title', $form->title)

    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm">
            <li class="inline-flex items-center">
                <a wire:navigate href="{{ route('student.dashboard') }}" class="inline-flex items-center text-slate-500 hover:text-[#0d9488]">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                    Dashboard
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-slate-700 font-medium md:ml-2">Isi Form</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Metadata Identitas Mahasiswa (Otomatis & Read-Only) -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">Identitas Mahasiswa Pengisi</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
            <div>
                <span class="text-slate-400 block text-xs">NIM</span>
                <span class="font-semibold text-slate-800">{{ Auth::guard('student')->user()->nim }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-xs">Nama Lengkap</span>
                <span class="font-semibold text-slate-800">{{ Auth::guard('student')->user()->name }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-xs">Email Pradita</span>
                <span class="font-semibold text-slate-800">{{ Auth::guard('student')->user()->email }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-xs">Angkatan</span>
                <span class="font-semibold text-slate-800">{{ Auth::guard('student')->user()->angkatan }}</span>
            </div>
        </div>
        <p class="text-[10px] text-slate-400 mt-3 font-medium flex items-center">
            <svg class="w-3.5 h-3.5 mr-1 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Data di atas diambil langsung dari sistem data kemahasiswaan dan bersifat read-only.
        </p>
    </div>

    <div x-data="formProgress()" class="relative">
        <!-- Sticky Progress Bar -->
        <div class="sticky top-0 z-40 bg-white shadow-md border-b border-slate-200 p-4 mb-6 transition-all duration-300" style="margin-left: -1rem; margin-right: -1rem; padding-left: 1rem; padding-right: 1rem;">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <div class="flex flex-col w-full">
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-sm font-bold text-slate-700">Progres Pengisian Formulir</span>
                        <span class="text-xs font-semibold text-teal-600" x-text="`${filledCount} dari ${totalRequired} pertanyaan wajib diisi (${percentage}%)`"></span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5">
                        <div class="bg-teal-500 h-2.5 rounded-full transition-all duration-500 ease-out" :style="`width: ${percentage}%`"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg border-t-4 border-[#0d9488] overflow-hidden mb-12">
            @if (session()->has('error'))
                <div class="m-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center">
                    <svg class="w-5 h-5 shrink-0 mr-3 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Header Info -->
            <div class="p-6 sm:p-10 border-b border-slate-100 bg-slate-50">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                    <h2 class="text-2xl font-bold text-[#0f172a]">{{ $form->title }}</h2>
                    <span class="px-3 py-1 bg-teal-100 text-teal-800 text-xs font-semibold rounded-full shrink-0">
                        {{ $form->activityType->name ?? 'Umum' }}
                    </span>
                </div>
                
                @if($form->description)
                    <p class="text-slate-600 mb-4 whitespace-pre-line text-sm leading-relaxed">{{ $form->description }}</p>
                @endif
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 rounded-lg border border-slate-200 text-xs">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium text-slate-900">Periode Akademik</p>
                            <p class="text-slate-500">Semester {{ $form->semester }} - Fase {{ $form->phase }}</p>
                        </div>
                    </div>
                    @if($form->close_at)
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="ml-3">
                                <p class="font-medium text-slate-900">Batas Waktu Pengisian</p>
                                <p class="text-red-600 font-semibold">{{ $form->close_at->format('d M Y, H:i') }} WIB</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Form Fields Render -->
            <div class="p-6 sm:p-10" @input.debounce="updateProgress()" @change="updateProgress()">
                <form wire:submit.prevent="submit" class="space-y-10">
                    
                    @php
                        // Group fields logically: Unsectioned first, then by section
                        $allFields = collect();
                        $globalFields = $form->fields->whereNull('section_id')->sortBy('order');
                        if($globalFields->count() > 0) {
                            $allFields->push(['type' => 'header', 'title' => 'Bagian Utama']);
                            foreach($globalFields as $f) { $allFields->push($f); }
                        }

                        foreach($form->sections->sortBy('order') as $sec) {
                            $secFields = $form->fields->where('section_id', $sec->id)->sortBy('order');
                            if($secFields->count() > 0) {
                                $allFields->push(['type' => 'header', 'title' => $sec->title, 'desc' => $sec->description]);
                                foreach($secFields as $f) { $allFields->push($f); }
                            }
                        }
                    @endphp

                    @foreach($allFields as $field)
                        @if(is_array($field) && $field['type'] === 'header')
                            <div class="border-b border-slate-200 pb-2 mt-8 mb-4">
                                <h3 class="text-xl font-bold text-slate-800">{{ $field['title'] }}</h3>
                                @if(isset($field['desc']) && $field['desc'])
                                    <p class="text-sm text-slate-500 mt-1">{{ $field['desc'] }}</p>
                                @endif
                            </div>
                        @else
                            @if($this->isFieldVisible($field))
                                @php
                                    $suffix = substr($field->name, strpos($field->name, '_') + 1);
                                    $isIdentity = in_array($suffix, ['nim', 'student_name', 'cohort']);
                                    $isFromParent = !empty($parentAnswers) && $this->findParentAnswer($suffix) !== null;
                                    $isReadOnly = $isIdentity || $isFromParent;
                                    $readonlyAttr = $isReadOnly ? 'readonly disabled' : '';
                                    $readonlyClass = $isReadOnly ? 'bg-slate-100 cursor-not-allowed opacity-80' : 'bg-white';
                                @endphp

                                <div class="bg-white p-5 rounded-lg border {{ $errors->has("responses.{$field->id}") || $errors->has("files.{$field->id}") ? 'border-red-300 ring-2 ring-red-100' : 'border-slate-200 hover:border-slate-300 transition-colors' }}">
                                    
                                    <!-- Label & Description -->
                                    <label class="block text-sm font-bold text-slate-800">
                                        {{ $field->label }} 
                                        @if($field->is_required) <span class="text-red-500 ml-1" title="Wajib Diisi">*</span> @endif
                                        @if($isReadOnly) <span class="ml-2 text-[10px] bg-slate-200 text-slate-600 px-2 py-0.5 rounded-full">Terkunci</span> @endif
                                    </label>

                                    @if($field->description)
                                        <p class="text-xs text-slate-500 mt-1">{{ $field->description }}</p>
                                    @endif

                                    <!-- Inputs by Type -->
                                    <div class="mt-2.5">
                                        @if($field->type === 'text')
                                            <input type="text" wire:model="responses.{{ $field->id }}" {{ $readonlyAttr }}
                                                @if($field->is_required) data-required="true" @endif
                                                placeholder="{{ $field->placeholder }}"
                                                class="progress-input mt-1 block w-full border-slate-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-[#0d9488] focus:border-[#0d9488] sm:text-sm {{ $readonlyClass }}">
                                        
                                        @elseif($field->type === 'textarea')
                                            <textarea wire:model="responses.{{ $field->id }}" rows="4" {{ $readonlyAttr }}
                                                @if($field->is_required) data-required="true" @endif
                                                placeholder="{{ $field->placeholder }}"
                                                class="progress-input mt-1 block w-full border-slate-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-[#0d9488] focus:border-[#0d9488] sm:text-sm {{ $readonlyClass }}"></textarea>

                                        @elseif($field->type === 'email')
                                            <input type="email" wire:model="responses.{{ $field->id }}" {{ $readonlyAttr }}
                                                @if($field->is_required) data-required="true" @endif
                                                placeholder="{{ $field->placeholder }}"
                                                class="progress-input mt-1 block w-full border-slate-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-[#0d9488] focus:border-[#0d9488] sm:text-sm {{ $readonlyClass }}">

                                        @elseif($field->type === 'tel')
                                            <input type="tel" wire:model="responses.{{ $field->id }}" {{ $readonlyAttr }}
                                                @if($field->is_required) data-required="true" @endif
                                                placeholder="{{ $field->placeholder }}"
                                                class="progress-input mt-1 block w-full border-slate-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-[#0d9488] focus:border-[#0d9488] sm:text-sm {{ $readonlyClass }}">

                                        @elseif($field->type === 'number')
                                            <input type="number" wire:model="responses.{{ $field->id }}" {{ $readonlyAttr }}
                                                @if($field->is_required) data-required="true" @endif
                                                placeholder="{{ $field->placeholder }}"
                                                class="progress-input mt-1 block w-full border-slate-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-[#0d9488] focus:border-[#0d9488] sm:text-sm {{ $readonlyClass }}">

                                        @elseif($field->type === 'url')
                                            <input type="url" wire:model="responses.{{ $field->id }}" {{ $readonlyAttr }}
                                                @if($field->is_required) data-required="true" @endif
                                                placeholder="{{ $field->placeholder ?? 'https://...' }}"
                                                class="progress-input mt-1 block w-full border-slate-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-[#0d9488] focus:border-[#0d9488] sm:text-sm {{ $readonlyClass }}">

                                        @elseif($field->type === 'date')
                                            <input type="date" wire:model="responses.{{ $field->id }}" {{ $readonlyAttr }}
                                                @if($field->is_required) data-required="true" @endif
                                                class="progress-input mt-1 block w-full border-slate-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-[#0d9488] focus:border-[#0d9488] sm:text-sm {{ $readonlyClass }}">

                                        @elseif($field->type === 'time')
                                            <input type="time" wire:model="responses.{{ $field->id }}" {{ $readonlyAttr }}
                                                @if($field->is_required) data-required="true" @endif
                                                class="progress-input mt-1 block w-full border-slate-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-[#0d9488] focus:border-[#0d9488] sm:text-sm {{ $readonlyClass }}">

                                        @elseif($field->type === 'date_range')
                                            <div class="grid grid-cols-2 gap-4">
                                                <input type="date" wire:model="responses.{{ $field->id }}.0" {{ $readonlyAttr }} @if($field->is_required) data-required="true" @endif class="progress-input block w-full border-slate-300 rounded-lg py-2 px-3 focus:ring-[#0d9488] sm:text-sm {{ $readonlyClass }}">
                                                <input type="date" wire:model="responses.{{ $field->id }}.1" {{ $readonlyAttr }} class="block w-full border-slate-300 rounded-lg py-2 px-3 focus:ring-[#0d9488] sm:text-sm {{ $readonlyClass }}">
                                            </div>

                                        @elseif($field->type === 'select' || $field->type === 'dropdown')
                                            <select wire:model="responses.{{ $field->id }}" {{ $readonlyAttr }}
                                                @if($field->is_required) data-required="true" @endif
                                                class="progress-input mt-1 block w-full border-slate-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-[#0d9488] focus:border-[#0d9488] sm:text-sm {{ $readonlyClass }}">
                                                <option value="">-- Pilih --</option>
                                                @if(is_array($field->options))
                                                    @foreach($field->options as $option)
                                                        <option value="{{ $option }}">{{ $option }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            
                                        @elseif($field->type === 'master_data')
                                            <select wire:model="responses.{{ $field->id }}" {{ $readonlyAttr }}
                                                @if($field->is_required) data-required="true" @endif
                                                class="progress-input mt-1 block w-full border-slate-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-[#0d9488] focus:border-[#0d9488] sm:text-sm {{ $readonlyClass }}">
                                                <option value="">-- Pilih --</option>
                                                @foreach($masterDataOptions[$field->id] ?? [] as $mId => $mLabel)
                                                    <option value="{{ $mId }}">{{ $mLabel }}</option>
                                                @endforeach
                                            </select>

                                        @elseif($field->type === 'radio')
                                            <div class="space-y-2 mt-2 progress-input-group" @if($field->is_required) data-required="true" @endif>
                                                @if(is_array($field->options))
                                                    @foreach($field->options as $option)
                                                        <label class="flex items-center text-sm">
                                                            <input type="radio" wire:model="responses.{{ $field->id }}" value="{{ $option }}" {{ $readonlyAttr }} class="h-4 w-4 text-[#0d9488] border-slate-300 focus:ring-[#0d9488] {{ $isReadOnly ? 'opacity-50' : '' }}">
                                                            <span class="ml-2 text-slate-700">{{ $option }}</span>
                                                        </label>
                                                    @endforeach
                                                @endif
                                            </div>

                                        @elseif($field->type === 'checkbox')
                                            <div class="space-y-2 mt-2 progress-input-group" @if($field->is_required) data-required="true" @endif>
                                                @if(is_array($field->options))
                                                    @foreach($field->options as $option)
                                                        <label class="flex items-center text-sm">
                                                            <input type="checkbox" wire:model="responses.{{ $field->id }}" value="{{ $option }}" {{ $readonlyAttr }} class="h-4.5 w-4.5 text-[#0d9488] border-slate-300 rounded focus:ring-[#0d9488] {{ $isReadOnly ? 'opacity-50' : '' }}">
                                                            <span class="ml-2 text-slate-700">{{ $option }}</span>
                                                        </label>
                                                    @endforeach
                                                @endif
                                            </div>

                                        @elseif($field->type === 'linear_scale')
                                            <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-lg mt-2 {{ $isReadOnly ? 'opacity-70' : '' }}">
                                                <span class="text-xs text-slate-500">Min</span>
                                                <div class="flex items-center space-x-6 progress-input-group" @if($field->is_required) data-required="true" @endif>
                                                    @for($i=1; $i<=5; $i++)
                                                        <label class="flex flex-col items-center">
                                                            <input type="radio" wire:model="responses.{{ $field->id }}" value="{{ $i }}" {{ $readonlyAttr }} class="text-[#0d9488] focus:ring-[#0d9488]">
                                                            <span class="text-xs text-slate-600 mt-1 font-bold">{{ $i }}</span>
                                                        </label>
                                                    @endfor
                                                </div>
                                                <span class="text-xs text-slate-500">Max</span>
                                            </div>

                                        @elseif($field->type === 'repeater')
                                            @php $opts = is_string($field->options) ? json_decode($field->options, true) : $field->options; @endphp
                                            @if(isset($opts['fields']))
                                                <div class="space-y-4">
                                                    @foreach($responses[$field->id] ?? [] as $index => $row)
                                                        <div class="p-4 border border-slate-200 rounded-lg bg-slate-50 relative">
                                                            <div class="flex justify-between items-center mb-3">
                                                                <h4 class="text-xs font-bold text-slate-500 uppercase">Item #{{ $index + 1 }}</h4>
                                                                @if(count($responses[$field->id]) > 1 && !$isReadOnly)
                                                                    <button type="button" wire:click="removeRepeaterRow({{ $field->id }}, {{ $index }})" class="text-red-500 hover:text-red-700 text-xs flex items-center">
                                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                                        Hapus
                                                                    </button>
                                                                @endif
                                                            </div>
                                                            
                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                                @foreach($opts['fields'] as $subField)
                                                                    <div>
                                                                        <label class="block text-xs font-semibold text-slate-700 mb-1">{{ $subField['label'] }}</label>
                                                                        @if($subField['type'] === 'text')
                                                                            <input type="text" wire:model="responses.{{ $field->id }}.{{ $index }}.{{ $subField['name'] }}" {{ $readonlyAttr }} @if($field->is_required) data-required="true" @endif class="progress-input block w-full border-slate-300 rounded-md py-2 px-3 text-sm focus:ring-teal-500 {{ $readonlyClass }}">
                                                                        @elseif($subField['type'] === 'number')
                                                                            <input type="number" wire:model="responses.{{ $field->id }}.{{ $index }}.{{ $subField['name'] }}" {{ $readonlyAttr }} @if($field->is_required) data-required="true" @endif class="progress-input block w-full border-slate-300 rounded-md py-2 px-3 text-sm focus:ring-teal-500 {{ $readonlyClass }}">
                                                                        @elseif($subField['type'] === 'master_data')
                                                                            <select wire:model="responses.{{ $field->id }}.{{ $index }}.{{ $subField['name'] }}" {{ $readonlyAttr }} @if($field->is_required) data-required="true" @endif class="progress-input block w-full border-slate-300 rounded-md py-2 px-3 text-sm focus:ring-teal-500 {{ $readonlyClass }}">
                                                                                <option value="">-- Pilih --</option>
                                                                                @foreach($masterDataOptions[$field->id . '_' . $subField['name']] ?? [] as $mId => $mLabel)
                                                                                    <option value="{{ $mId }}">{{ $mLabel }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        @endif
                                                                        @error("responses.{$field->id}.{$index}.{$subField['name']}") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    
                                                    @if(!$isReadOnly)
                                                    <button type="button" wire:click="addRepeaterRow({{ $field->id }})" class="mt-2 text-sm font-semibold text-teal-600 hover:text-teal-700 flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                                        Tambah {{ $field->label }}
                                                    </button>
                                                    @endif
                                                </div>
                                            @endif

                                        @elseif($field->type === 'file')
                                            <div class="mt-1 flex items-center p-4 border-2 border-dashed border-slate-300 rounded-lg hover:bg-slate-50 transition-colors {{ $isReadOnly ? 'hidden' : '' }}">
                                                <input type="file" wire:model="files.{{ $field->id }}" 
                                                    @if($field->max_files && $field->max_files > 1) multiple @endif
                                                    @if($field->is_required) data-required="true" @endif
                                                    class="progress-input block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-[#0d9488] hover:file:bg-teal-100 cursor-pointer">
                                            </div>
                                            <p class="text-[11px] text-slate-500 mt-2 flex items-center">
                                                <svg class="w-3.5 h-3.5 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                @if($field->allowed_types) Format diizinkan: <strong>{{ implode(', ', $field->allowed_types) }}</strong>. @endif
                                                @if($field->max_size_mb) Maksimal: <strong>{{ $field->max_size_mb }} MB</strong> per file. @endif
                                                @if($field->max_files && $field->max_files > 1) Maksimal: <strong>{{ $field->max_files }} berkas</strong>. @endif
                                            </p>
                                            
                                            <div wire:loading wire:target="files.{{ $field->id }}" class="text-xs text-teal-600 mt-2 font-medium flex items-center">
                                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-teal-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Mengunggah file...
                                            </div>
                                        @endif
                                    </div>

                                    @error("responses.{$field->id}") 
                                        <span class="text-red-500 text-xs mt-2 block flex items-center"><svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>{{ $message }}</span> 
                                    @enderror
                                    @error("files.{$field->id}") 
                                        <span class="text-red-500 text-xs mt-2 block flex items-center"><svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>{{ $message }}</span> 
                                    @enderror
                                </div>
                            @endif
                        @endif
                    @endforeach

                    <!-- Submission Actions -->
                    <div class="pt-8 border-t border-slate-200 text-right">
                        <button type="submit" 
                            wire:loading.attr="disabled"
                            @if($isSubmitting) disabled @endif
                            class="bg-[#0f172a] hover:bg-slate-800 border border-transparent rounded-lg shadow-md py-3 px-8 inline-flex justify-center items-center text-base font-semibold text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 transition-colors disabled:opacity-70 disabled:cursor-not-allowed w-full md:w-auto">
                            <span wire:loading.remove wire:target="submit" class="flex items-center">
                                Kirim Formulir
                                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </span>
                            <span wire:loading wire:target="submit" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('formProgress', () => ({
                totalRequired: 0,
                filledCount: 0,
                percentage: 0,
                init() {
                    // Update periodically just in case Livewire mutates DOM
                    this.updateProgress();
                    setInterval(() => this.updateProgress(), 1000);
                },
                updateProgress() {
                    let total = 0;
                    let filled = 0;

                    // Text, Date, Select inputs
                    const inputs = document.querySelectorAll('.progress-input[data-required="true"]');
                    inputs.forEach(input => {
                        total++;
                        if (input.value && input.value.trim() !== '') {
                            filled++;
                        }
                    });

                    // Groups (Radio, Checkbox)
                    const groups = document.querySelectorAll('.progress-input-group[data-required="true"]');
                    groups.forEach(group => {
                        total++;
                        const checked = group.querySelector('input:checked');
                        if (checked) {
                            filled++;
                        }
                    });

                    this.totalRequired = total;
                    this.filledCount = filled;
                    this.percentage = total === 0 ? 100 : Math.round((filled / total) * 100);
                }
            }));
        });

        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('commit', ({ component, succeed }) => {
                succeed(() => {
                    const firstError = document.querySelector('.border-red-300');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            });
        });
    </script>
</div>
