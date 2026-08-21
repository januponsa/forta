import sys

file_path = r'c:\Users\userJ\Documents\fortain\app\Livewire\Student\StudentFormFiller.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

replacement = """            if ($field->type === 'file' && !empty($this->files[$field->id])) {
                $uploaded = $this->files[$field->id];
                $maxKb = ($field->max_size_mb ?: 2) * 1024;
                $allowed = is_array($field->allowed_types) ? strtolower(implode(',', $field->allowed_types)) : 'pdf,jpg,png,docx,xlsx,zip';

                if (is_array($uploaded)) {
                    $rules["files.{$field->id}"] = "array|max:{$field->max_files}";
                    $messages["files.{$field->id}.max"] = "Maksimal berkas {$field->label} adalah {$field->max_files}.";
                    foreach($uploaded as $idx => $f) {
                        if ($f instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                            $rules["files.{$field->id}.{$idx}"] = "file|max:{$maxKb}|mimes:{$allowed}";
                            $messages["files.{$field->id}.{$idx}.max"] = "Ukuran {$field->label} melebihi {$field->max_size_mb} MB.";
                            $messages["files.{$field->id}.{$idx}.mimes"] = "Format {$field->label} harus: {$allowed}.";
                        }
                    }
                } else {
                    if ($uploaded instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                        $rules["files.{$field->id}"] = "file|max:{$maxKb}|mimes:{$allowed}";
                        $messages["files.{$field->id}.max"] = "Ukuran {$field->label} melebihi {$field->max_size_mb} MB.";
                        $messages["files.{$field->id}.mimes"] = "Format {$field->label} harus: {$allowed}.";
                    }
                }
            }"""

# Find the block to replace
old_block = """            if ($field->type === 'file' && isset($this->files[$field->id])) {
                $uploaded = $this->files[$field->id];
                $maxKb = ($field->max_size_mb ?: 2) * 1024;
                $allowed = is_array($field->allowed_types) ? strtolower(implode(',', $field->allowed_types)) : 'pdf,jpg,png,docx,xlsx,zip';

                if (is_array($uploaded)) {
                    $rules["files.{$field->id}"] = "array|max:{$field->max_files}";
                    $messages["files.{$field->id}.max"] = "Maksimal berkas {$field->label} adalah {$field->max_files}.";
                    $rules["files.{$field->id}.*"] = "file|max:{$maxKb}|mimes:{$allowed}";
                    $messages["files.{$field->id}.*.max"] = "Ukuran {$field->label} melebihi {$field->max_size_mb} MB.";
                    $messages["files.{$field->id}.*.mimes"] = "Format {$field->label} harus: {$allowed}.";
                } else {
                    $rules["files.{$field->id}"] = "file|max:{$maxKb}|mimes:{$allowed}";
                    $messages["files.{$field->id}.max"] = "Ukuran {$field->label} melebihi {$field->max_size_mb} MB.";
                    $messages["files.{$field->id}.mimes"] = "Format {$field->label} harus: {$allowed}.";
                }
            }"""

content = content.replace(old_block, replacement)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
