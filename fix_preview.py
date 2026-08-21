import sys
import re

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\livewire\admin\form-field-builder.blade.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the multi-page preview logic with single-page
pattern = r"<!-- Dynamic Render of Current Mode Pages -->.*?<!-- Wizard Buttons -->.*?</div>.*?</div>"
replacement = """<!-- Dynamic Render of Single Page Form -->
                    @php
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

                    <!-- Render Questions -->
                    <div class="space-y-4">
                        @forelse($allFields as $field)
                            @if(is_array($field) && $field['type'] === 'header')
                                <div class="border-b border-slate-200 pb-2 mt-8 mb-4">
                                    <h3 class="text-xl font-bold text-slate-800">{{ $field['title'] }}</h3>
                                    @if(isset($field['desc']) && $field['desc'])
                                        <p class="text-sm text-slate-500 mt-1">{{ $field['desc'] }}</p>
                                    @endif
                                </div>
                            @else
                                @if($field->is_active)
                                    <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-2">
                                        
                                        <!-- Label & Required -->
                                        <label class="block text-sm font-bold text-slate-800">
                                            {{ $field->label }}
                                            @if($field->is_required)
                                                <span class="text-red-500 ml-0.5">*</span>
                                            @endif
                                        </label>

                                        <!-- Description -->
                                        @if($field->description)
                                            <p class="text-xs text-slate-500">{{ $field->description }}</p>
                                        @endif

                                        <!-- Render inputs dynamically based on type -->
                                        @if($field->type === 'text')
                                            <input type="text" placeholder="{{ $field->placeholder }}" class="w-full border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        @elseif($field->type === 'textarea')
                                            <textarea rows="3" placeholder="{{ $field->placeholder }}" class="w-full border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                                        @elseif($field->type === 'email')
                                            <input type="email" placeholder="{{ $field->placeholder }}" class="w-full border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        @elseif($field->type === 'tel')
                                            <input type="tel" placeholder="{{ $field->placeholder }}" class="w-full border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        @elseif($field->type === 'number')
                                            <input type="number" placeholder="{{ $field->placeholder }}" class="w-full border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        @elseif($field->type === 'url')
                                            <input type="url" placeholder="{{ $field->placeholder }}" class="w-full border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        @elseif($field->type === 'date')
                                            <input type="date" class="w-full border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        @elseif($field->type === 'time')
                                            <input type="time" class="w-full border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        @elseif($field->type === 'date_range')
                                            <div class="grid grid-cols-2 gap-3">
                                                <input type="date" class="border-slate-300 rounded-lg py-2 px-3 text-sm">
                                                <input type="date" class="border-slate-300 rounded-lg py-2 px-3 text-sm">
                                            </div>
                                        @elseif($field->type === 'select' || $field->type === 'dropdown')
                                            <select class="w-full bg-white border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">-- Pilih --</option>
                                                @if(is_array($field->options))
                                                    @foreach($field->options as $opt)
                                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        @elseif($field->type === 'radio')
                                            <div class="space-y-1.5">
                                                @if(is_array($field->options))
                                                    @foreach($field->options as $opt)
                                                        <label class="flex items-center text-sm">
                                                            <input type="radio" name="preview_radio_{{ $field->id }}" value="{{ $opt }}" class="h-4 w-4 text-blue-600 border-slate-300 focus:ring-blue-500">
                                                            <span class="ml-2 text-slate-700">{{ $opt }}</span>
                                                        </label>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @elseif($field->type === 'checkbox')
                                            <div class="space-y-1.5">
                                                @if(is_array($field->options))
                                                    @foreach($field->options as $opt)
                                                        <label class="flex items-center text-sm">
                                                            <input type="checkbox" value="{{ $opt }}" class="h-4.5 w-4.5 text-blue-600 border-slate-300 rounded">
                                                            <span class="ml-2 text-slate-700">{{ $opt }}</span>
                                                        </label>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @elseif($field->type === 'linear_scale')
                                            <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-lg">
                                                <span class="text-xs text-slate-500">Min</span>
                                                <div class="flex items-center space-x-4">
                                                    @for($i=1; $i<=5; $i++)
                                                        <label class="flex flex-col items-center">
                                                            <input type="radio" name="preview_linear_{{ $field->id }}" class="text-blue-600 focus:ring-blue-500">
                                                            <span class="text-xs text-slate-600 mt-1 font-bold">{{ $i }}</span>
                                                        </label>
                                                    @endfor
                                                </div>
                                                <span class="text-xs text-slate-500">Max</span>
                                            </div>
                                        @elseif($field->type === 'file')
                                            <div class="flex flex-col items-center justify-center border-2 border-dashed border-slate-300 rounded-lg p-4 bg-slate-50">
                                                <p class="text-xs text-slate-500">Unggah File (Simulasi)</p>
                                                <p class="text-[10px] text-slate-400 mt-0.5">Maks: {{ $field->max_files }} file ({{ $field->max_size_mb }}MB) / {{ is_array($field->allowed_types) ? implode(', ', $field->allowed_types) : 'Bebas' }}</p>
                                            </div>
                                        @elseif($field->type === 'section_title')
                                            <div class="border-b border-slate-100 pb-2">
                                                <h4 class="text-sm font-bold text-slate-900">{{ $field->label }}</h4>
                                            </div>
                                        @elseif($field->type === 'info')
                                            <div class="bg-slate-50 rounded-lg p-3 text-xs text-slate-600 border border-slate-150">
                                                {{ $field->label }}
                                            </div>
                                        @elseif($field->type === 'divider')
                                            <hr class="border-slate-200 my-2">
                                        @endif

                                    </div>
                                @endif
                            @endif
                        @empty
                            <p class="text-center text-slate-400 text-xs py-4">Belum ada pertanyaan.</p>
                        @endforelse
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-between items-center bg-white border-t border-slate-200 px-6 py-4 rounded-xl mt-4">
                        <div class="w-full text-right">
                            <button type="button" class="bg-emerald-600 text-white font-semibold py-2 px-6 rounded-lg text-sm disabled:opacity-70 cursor-not-allowed">
                                Submit (Simulasi)
                            </button>
                        </div>
                    </div>"""

new_content = re.sub(pattern, replacement, content, flags=re.DOTALL)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(new_content)
