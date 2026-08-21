import sys

file_path = r'c:\Users\userJ\Documents\fortain\app\Livewire\Student\StudentFormFiller.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

replacement = """                    foreach ($fileArray as $file) {
                        $originalName = $file->getClientOriginalName();
                        $mimeType = $file->getMimeType();
                        $sizeBytes = $file->getSize();

                        $path = $file->store('submissions/'.$this->form->id, 'local');
                        $uploadedPaths[] = $path;

                        SubmissionFile::create([
                            'submission_id' => $submission->id,
                            'field_id' => $field->id,
                            'stored_path' => $path,
                            'original_name' => $originalName,
                            'mime_type' => $mimeType,
                            'size_bytes' => $sizeBytes,
                            'uploaded_at' => now(),
                        ]);
                    }"""

old_block = """                    foreach ($fileArray as $file) {
                        $path = $file->store('submissions/'.$this->form->id, 'local');
                        $uploadedPaths[] = $path;

                        SubmissionFile::create([
                            'submission_id' => $submission->id,
                            'field_id' => $field->id,
                            'stored_path' => $path,
                            'original_name' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'size_bytes' => $file->getSize(),
                            'uploaded_at' => now(),
                        ]);
                    }"""

content = content.replace(old_block, replacement)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
