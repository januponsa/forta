import sys

file_path = r'C:\Users\userJ\.gemini\antigravity-ide\brain\979955bb-067b-46fd-a536-e9b7fcf55a48\task.md'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace(
    "- `[ ]` **3. Single Page Form Mahasiswa & Preview**",
    "- `[x]` **3. Single Page Form Mahasiswa & Preview**"
).replace(
    "  - `[ ]` Hapus logika Next/Previous di `StudentFormFiller.php` dan `form-manager.blade.php`.",
    "  - `[x]` Hapus logika Next/Previous di `StudentFormFiller.php` dan `form-manager.blade.php`."
).replace(
    "  - `[ ]` Tampilkan semua section dalam satu halaman.",
    "  - `[x]` Tampilkan semua section dalam satu halaman."
).replace(
    "  - `[ ]` Satu tombol Kirim Form di bawah.",
    "  - `[x]` Satu tombol Kirim Form di bawah."
).replace(
    "- `[ ]` **4. Progress Bar Real-time**",
    "- `[x]` **4. Progress Bar Real-time**"
).replace(
    "  - `[ ]` Tambahkan progress bar sticky di frontend (Alpine.js).",
    "  - `[x]` Tambahkan progress bar sticky di frontend (Alpine.js)."
).replace(
    "  - `[ ]` Hitung hanya field yang harus diisi.",
    "  - `[x]` Hitung hanya field yang harus diisi."
).replace(
    "  - `[ ]` Handle kondisional field.",
    "  - `[x]` Handle kondisional field."
)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
