import sys

file_path = r'C:\Users\userJ\.gemini\antigravity-ide\brain\979955bb-067b-46fd-a536-e9b7fcf55a48\task.md'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace(
    "- `[ ]` **1. Manajemen Semester Akademik**",
    "- `[x]` **1. Manajemen Semester Akademik**"
).replace(
    "  - `[ ]` Buat komponen Livewire untuk manajemen semester di dalam Form Manager.",
    "  - `[x]` Buat komponen Livewire untuk manajemen semester di dalam Form Manager."
).replace(
    "  - `[ ]` Gunakan model `AcademicCalendar`.",
    "  - `[x]` Gunakan model `AcademicCalendar`."
).replace(
    "  - `[ ]` Tambahkan validasi tanggal tumpang tindih.",
    "  - `[x]` Tambahkan validasi tanggal tumpang tindih."
).replace(
    "  - `[ ]` Jangan hapus semester jika ada form.",
    "  - `[x]` Jangan hapus semester jika ada form."
).replace(
    "  - `[ ]` Hapus permanen hanya untuk admin_forta.",
    "  - `[x]` Hapus permanen hanya untuk admin_forta."
).replace(
    "  - `[ ]` Simpan state filter semester.",
    "  - `[x]` Simpan state filter semester."
).replace(
    "- `[ ]` **2. Perbaikan Tombol Aksi Form**",
    "- `[x]` **2. Perbaikan Tombol Aksi Form**"
).replace(
    "  - `[ ]` Fields: Cek stabilitas, pastikan reorder jalan.",
    "  - `[x]` Fields: Cek stabilitas, pastikan reorder jalan."
).replace(
    "  - `[ ]` Versi Baru: Ubah increment version, status draft, tidak salin submission.",
    "  - `[x]` Versi Baru: Ubah increment version, status draft, tidak salin submission."
).replace(
    "  - `[ ]` Duplikat: Buat standalone form, tambah prefix \"Salinan - \", form_code unik.",
    "  - `[x]` Duplikat: Buat standalone form, tambah prefix \"Salinan - \", form_code unik."
).replace(
    "  - `[ ]` Edit: Pertahankan input saat validasi gagal.",
    "  - `[x]` Edit: Pertahankan input saat validasi gagal."
).replace(
    "  - `[ ]` Aktifkan: Validasi minimal 1 field dan tanggal valid.",
    "  - `[x]` Aktifkan: Validasi minimal 1 field dan tanggal valid."
).replace(
    "  - `[ ]` Tutup: Ubah status ke closed.",
    "  - `[x]` Tutup: Ubah status ke closed."
).replace(
    "  - `[ ]` Arsipkan: Soft-delete dan ubah status ke archived.",
    "  - `[x]` Arsipkan: Soft-delete dan ubah status ke archived."
).replace(
    "  - `[ ]` Hapus: Hapus jika tidak ada submission.",
    "  - `[x]` Hapus: Hapus jika tidak ada submission."
).replace(
    "- `[ ]` **5. Tata Letak Tombol**",
    "- `[x]` **5. Tata Letak Tombol**"
).replace(
    "  - `[ ]` Perbaiki tinggi dan lebar tombol `Bersihkan Filter` dan `Atur Urutan`.",
    "  - `[x]` Perbaiki tinggi dan lebar tombol `Bersihkan Filter` dan `Atur Urutan`."
).replace(
    "- `[ ]` **6. Atur Urutan Form**",
    "- `[x]` **6. Atur Urutan Form**"
).replace(
    "  - `[ ]` Pastikan drag-and-drop / naik turun tersimpan permanen secara transaction-safe.",
    "  - `[x]` Pastikan drag-and-drop / naik turun tersimpan permanen secara transaction-safe."
)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
