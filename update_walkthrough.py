import sys

file_path = r'C:\Users\userJ\.gemini\antigravity-ide\brain\979955bb-067b-46fd-a536-e9b7fcf55a48\walkthrough.md'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

new_content = """# Pembaruan Kelola Form, Preview & Admin User

Seluruh perbaikan pada modul Form dan Admin User telah diselesaikan sesuai dengan rencana implementasi yang disetujui.

## 1. Manajemen Semester di Kelola Form
- **Kelola Semester**: Menambahkan tombol "Kelola Semester" di atas daftar Form yang akan membuka Modal khusus manajemen semester.
- **CRUD Semester**: Admin dapat melihat tabel semester, menambah, mengedit, dan menghapus semester. 
- **Validasi Keamanan**: Semester tidak dapat dihapus jika masih ada form yang terhubung ke semester tersebut. Semua fitur ini terintegrasi menggunakan model `AcademicCalendar`.

## 2. Perbaikan Fitur Tombol Aksi Form
Semua tombol aksi pada form telah diperbaiki dan distabilkan menggunakan `DB::beginTransaction()` untuk keamanan data:
- **Aktifkan**: Akan mengecek minimal ketersediaan 1 field/pertanyaan.
- **Duplikat**: Menciptakan form baru (standalone) dengan suffix 'copy' dan 'Salinan', tanpa menyalin submission.
- **Versi Baru**: Mengubah versi secara inkremental, mengatur `parent_form_id`, serta mempertahankan `form_code` agar relasi versi dapat terlacak dengan benar.
- **Hapus Permanen**: Menghapus form beserta field dan section-nya secara permanen. Form tidak akan terhapus jika sudah memiliki submission mahasiswa.
- **Arsipkan & Restore**: Mengubah status menjadi `archived` / `draft` menggunakan fitur `SoftDeletes`.

## 3. UI Kelola Form (Admin)
- **Tampilan Tombol Filter**: Memperbaiki flex layout tombol "Bersihkan Filter" dan "Atur Urutan" agar sejajar, lebih rapi dan konsisten (baik untuk versi web maupun mobile).
- **Atur Urutan**: Proses seret-lepas dan simpan urutan dipastikan menyimpan urutan secara aman berdasarkan filter Semester yang aktif.

## 4. Single-Page Form (Preview & Mahasiswa)
- **Preview Form (Admin)**: Telah diperbarui sehingga menampilkan seluruh field secara bersamaan dengan pemisah section (Single Page Layout), menghilangkan fungsi *Next/Previous*.
- **Pengisian Form (Mahasiswa)**: Sepenuhnya telah mengadopsi Single Page Layout di komponen `StudentFormFiller.php` dan tampilan *blade*-nya, sehingga proses pengisian menjadi satu kesatuan (*seamless*).

## 5. Perbaikan Bug Menu Admin User
- **Masalah Pop-up Error**: Memperbaiki isu di mana setiap tombol (Edit, Tambah, Hapus) pada menu **Admin User** mengalami konflik DOM Snapshot / Pop-up Error pada Livewire 3.
- **Solusi**: Menambahkan tag `wire:key` pada list loop, serta `wire:ignore.self` pada modal AlpineJS sehingga komponen Livewire tidak kehilangan status saat DOM dire-render.

## 6. Cara Verifikasi

1. **Test Kelola Form & Semester**: Buka menu Kelola Form, klik tombol "Kelola Semester". Cobalah membuat, mengedit, dan menghapus (dengan syarat tidak ada form terikat).
2. **Test Aksi Form**: Buat satu form percobaan. Tekan tombol Duplikat, Arsipkan, dan Hapus Permanen secara bergantian.
3. **Test Single Page Preview**: Klik "Preview" pada form yang Anda buat dan cek apakah bentuknya sudah dalam 1 halaman utuh (termasuk saat dibuka dari menu mahasiswa).
4. **Test Admin User**: Buka menu Admin User, pastikan klik pada tombol *Edit*, *Tambah Admin*, dan *Hapus* sudah berfungsi dengan lancar tanpa pop-up error.

---

"""

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(new_content + "\n" + content)
