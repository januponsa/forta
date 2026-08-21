import sys

file_path = r'c:\Users\userJ\Documents\fortain\app\Http\Controllers\SubmissionFileController.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

replacement = """    private function processDownload(SubmissionFile $file)
    {
        $headers = [
            'Content-Type' => $file->mime_type ?? 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . addslashes($file->original_name) . '"'
        ];

        if (!Storage::disk('local')->exists($file->stored_path)) {
            // Check public disk for backwards compatibility in case of older uploads
            if (Storage::disk('public')->exists($file->stored_path)) {
                $publicPath = Storage::disk('public')->path($file->stored_path);
                return response()->file($publicPath, $headers);
            }
            abort(404, 'File not found on server.');
        }

        $localPath = Storage::disk('local')->path($file->stored_path);
        return response()->file($localPath, $headers);
    }"""

import re
pattern = re.compile(r'\s*private function processDownload\(SubmissionFile \$file\).*?return response\(\)->file\(\$localPath\);\s*}', re.DOTALL)
content = pattern.sub('\n' + replacement, content)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
