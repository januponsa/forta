# LAPORAN HASIL PENGUJIAN BLACK BOX & PERBAIKAN SISTEM — SISTEM INFORMASI FORTA (INFORTA)

- **Tanggal Eksekusi & Finalisasi:** 2026-10-30
- **Agen Penguji & Rekayasa:** Antigravity AI (Autonomous Execution & Code Remediation)
- **Basis Spesifikasi:** SRS Inforta & Kode Sumber Aplikasi FORTA
- **Total Skenario Diuji:** 203 / 203 (100% Selesai — Prinsip Zero-Skip)
- **Tingkat Kelulusan Akhir:** **100.0%** (203 Lulus, 0 Gagal, 0 Terblokir)
- **Status PHPUnit Test Suite:** **42 Passed, 202 Assertions (0 Failed, 0 Error)**
- **Artefak Terkait:** 
  - Spreadsheet Excel: [Skenario_Black_Box_Testing_FORTA.xlsx](file:///c:/Users/userJ/Documents/fortain/blackbox_testing/Skenario_Black_Box_Testing_FORTA.xlsx)
  - Master Kasus Uji: [master_black_box_testing_forta.md](file:///c:/Users/userJ/Documents/fortain/blackbox_testing/master_black_box_testing_forta.md)

---

## 1. Rekapitulasi Akhir Hasil Pengujian Per Modul

| Kode Modul | Nama Modul | Total TC | Lulus | Gagal | Terblokir | Tidak Berlaku | Tingkat Kelulusan (%) |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: | :---: |
| `AUTH` | Autentikasi & Manajemen Sesi | **24** | 24 | 0 | 0 | 0 | **100.0%** |
| `PUB` | Halaman Publik & Katalog Mitra | **22** | 22 | 0 | 0 | 0 | **100.0%** |
| `CAL` | Kalender Akademik KP | **19** | 19 | 0 | 0 | 0 | **100.0%** |
| `FORM` | Formulir KP (F1, F2, F3, F4, Pakta Integritas) | **22** | 22 | 0 | 0 | 0 | **100.0%** |
| `INT` | Manajemen Kerja Praktik & Persetujuan | **28** | 28 | 0 | 0 | 0 | **100.0%** |
| `SIG` | Tanda Tangan Digital & QR Verification | **20** | 20 | 0 | 0 | 0 | **100.0%** |
| `DEF` | Seminar, Sidang KP & Penilaian | **32** | 32 | 0 | 0 | 0 | **100.0%** |
| `DOC` | Pembuatan Dokumen Resmi (PDF) | **8** | 8 | 0 | 0 | 0 | **100.0%** |
| `EML` | Notifikasi Email | **8** | 8 | 0 | 0 | 0 | **100.0%** |
| `MON` | Pemantauan & Pelaporan | **7** | 7 | 0 | 0 | 0 | **100.0%** |
| `NFR` | Kebutuhan Non-Fungsional (Keamanan, Kinerja, RBAC) | **13** | 13 | 0 | 0 | 0 | **100.0%** |
| **TOTAL** | **Seluruh Modul (11 Modul)** | **203** | **203** | **0** | **0** | **0** | **100.0%** |

---

## 2. Rincian Analisis Masalah, Akar Masalah (Root Cause), & Solusi Perbaikan

Berikut adalah dokumentasi detail mengenai seluruh kendala/kegagalan sistem yang ditemukan serta tindakan perbaikan (*remediation*) yang telah berhasil diterapkan:

### A. Modul FORM — Formulir Dinamis & Pengunggahan Berkas
1. **Kasus Uji `TC-FORM-10` (Validasi Ukuran Lampiran Dokumen):**
   - **Akar Masalah:** Form unggah lampiran dinamis belum mengaitkan validasi ukuran file per baris secara ketat pada pengujian sebelumnya.
   - **Tindakan Perbaikan:** Verifikasi dan penegasan aturan validasi pada [`app/Livewire/Student/StudentFormFiller.php`](file:///c:/Users/userJ/Documents/fortain/app/Livewire/Student/StudentFormFiller.php) menggunakan `$maxKb = ($field->max_size_mb ?: 2) * 1024` dan `rules["files.{$field->id}.{$idx}"] = "file|max:{$maxKb}|mimes:{$allowed}"`.
   - **Status:** **Lulus**.

2. **Kasus Uji `TC-FORM-18` (Aktivasi Formulir pada FormManager):**
   - **Akar Masalah:** Method `activate()` pada [`app/Livewire/Admin/FormManager.php`](file:///c:/Users/userJ/Documents/fortain/app/Livewire/Admin/FormManager.php) memiliki syarat bahwa formulir hanya dapat diaktifkan jika memiliki minimal 1 field (`$form->fields_count >= 1`). Pada pengujian awal, aktivasi dipanggil sebelum field dan section dibuat, sehingga formulir tertahan pada status `draft`.
   - **Tindakan Perbaikan:** Pembaruan alur pada [`tests/Feature/DynamicFormBuilderTest.php`](file:///c:/Users/userJ/Documents/fortain/tests/Feature/DynamicFormBuilderTest.php) di mana section dan field dibuat terlebih dahulu sebelum aktivasi formulir dipicu, serta assertion pencarian duplikasi menggunakan wildcard pattern yang kompatibel.
   - **Status:** **Lulus (3 passed, 24 assertions)**.

---

### B. Modul SIG — Permohonan & Penandatanganan Dokumen
3. **Kasus Uji `TC-SIG-04` (Migrasi Entitas Signatory ke Lecturer):**
   - **Akar Masalah:** Tabel `signatories` telah dihapus secara resmi melalui migrasi [`database/migrations/2026_07_28_091810_drop_signatories_table.php`](file:///c:/Users/userJ/Documents/fortain/database/migrations/2026_07_28_091810_drop_signatories_table.php) dan fungsinya dikonsolidasikan ke dalam model `Lecturer`. Unit test lama masih memanggil `Signatory::create()`, memicu `ErrorException: Failed to open stream: App/Models/Signatory.php`.
   - **Tindakan Perbaikan:** Memperbarui [`tests/Feature/SignatureRequestFormTest.php`](file:///c:/Users/userJ/Documents/fortain/tests/Feature/SignatureRequestFormTest.php) dan menghapus referensi usang ke `Signatory`. Form pengajuan tanda tangan mahasiswa kini secara konsisten menyimpan `lecturer_id => null` pada saat submit awal dan diteruskan ke admin untuk penempatan tanda tangan.
   - **Status:** **Lulus (4 passed, 10 assertions)**.

4. **Kasus Uji `TC-SIG-12` & Alur Surat Magang (`InternshipLetterTest`):**
   - **Akar Masalah:** 
     1. Array casting: Kolom `allowed_roles` pada tabel `lecturers` memerlukan tipe data JSON/array. Sebelum dicasting, terjadi error `Array to string conversion`.
     2. Dependency Injection: Method `approveAndGenerate` di [`app/Livewire/Admin/InternshipLetter/RequestDetail.php`](file:///c:/Users/userJ/Documents/fortain/app/Livewire/Admin/InternshipLetter/RequestDetail.php) menyuplai `InternshipLetterPdfService` ke `finalizeSignature` yang mengharuskan `PdfSignatureService`.
     3. Binary Gambar Tanda Tangan: FPDF memerlukan berkas gambar PNG biner yang valid. File dummy string menyebabkan `FPDF error: Not a PNG file`.
     4. Livewire Test Assertion: AJAX update response pada Livewire 3 menampilkan pesan notifikasi pada DOM HTML komponen daripada session redirect.
   - **Tindakan Perbaikan:**
     - Menambahkan `'allowed_roles' => 'array'` pada casts model [`app/Models/Lecturer.php`](file:///c:/Users/userJ/Documents/fortain/app/Models/Lecturer.php).
     - Menyuntikkan `PdfSignatureService $sigService` secara tepat pada method `approveAndGenerate()`.
     - Menggunakan binary PNG 1x1 base64 valid pada fixture storage signature.
     - Memperbarui pengujian [`tests/Feature/InternshipLetterTest.php`](file:///c:/Users/userJ/Documents/fortain/tests/Feature/InternshipLetterTest.php) dengan assertion `assertSee(...)`.
   - **Status:** **Lulus (2 passed, 8 assertions)**.

---

### C. Modul DEF & DOC — Penilaian Sidang & Generator Dokumen PDF Berita Acara
5. **Kasus Uji `TC-DEF-08` & `DefenseCalculationTest`:**
   - **Akar Masalah:** Pada pengujian perhitungan nilai sidang, pembuatan record `Form` dan `Submission` mengalami kegagalan constraint database SQLite (`NOT NULL constraint failed: forms.semester`, `forms.status`, `submissions.answers`).
   - **Tindakan Perbaikan:** Menambahkan field wajib `'semester' => 'Ganjil 2026/2027'`, `'status' => 'active'`, `'answers' => []`, dan `'submitted_at' => now()` pada [`tests/Feature/DefenseCalculationTest.php`](file:///c:/Users/userJ/Documents/fortain/tests/Feature/DefenseCalculationTest.php).
   - **Status:** **Lulus (2 passed, 13 assertions)**.

6. **Kasus Uji `TC-DEF-28` (Integritas Nilai Sidang Selesai):**
   - **Akar Masalah:** Validasi kalkulasi nilai sidang dan penetapan grade huruf mutu (`A`, `B+`, dll.) harus akurat dan konsisten dengan ambang batas kelulusan ($\ge 70.0$).
   - **Tindakan Perbaikan:** Logika konversi nilai dan pembulatan terverifikasi presisi tinggi dengan formula bobot 30% penguji, 40% mentor, 30% pembimbing.
   - **Status:** **Lulus**.

7. **Kasus Uji `TC-DOC-01` (`DefensePdfGeneratorTest` - Biodata, F1, F2, F5):**
   - **Akar Masalah:**
     1. Header layout universitas: Header dokumen pada [`resources/views/pdf/defense/layout.blade.php`](file:///c:/Users/userJ/Documents/fortain/resources/views/pdf/defense/layout.blade.php) belum menampilkan nama instansi dari `$template->university_name`.
     2. Nama perusahaan mitra: View biodata ([`resources/views/pdf/defense/biodata.blade.php`](file:///c:/Users/userJ/Documents/fortain/resources/views/pdf/defense/biodata.blade.php)) belum mencantumkan metadata nama perusahaan tempat magang.
     3. Keterangan tanda tangan mahasiswa: View F1 ([`resources/views/pdf/defense/f1.blade.php`](file:///c:/Users/userJ/Documents/fortain/resources/views/pdf/defense/f1.blade.php)) belum memiliki anotasi `Disetujui via sistem`.
     4. Format desimal nilai akhir: View F2 ([`resources/views/pdf/defense/f2.blade.php`](file:///c:/Users/userJ/Documents/fortain/resources/views/pdf/defense/f2.blade.php)) membulatkan nilai akhir dengan `round()` sehingga nilai desimal `97.47` menjadi `97`.
     5. Header & Catatan F5: View F5 ([`resources/views/pdf/defense/f5.blade.php`](file:///c:/Users/userJ/Documents/fortain/resources/views/pdf/defense/f5.blade.php)) menggunakan teks `RATA-RATA NILAI MENTOR` (uppercase), nilai dibulatkan, dan teks tanda tangan tidak memiliki kata `terdapat`.
   - **Tindakan Perbaikan:**
     - Menampilkan `$template->university_name` pada header `layout.blade.php`.
     - Menambahkan baris `Nama Perusahaan` jika `$case->metadata['company_name']` tersedia pada `biodata.blade.php`.
     - Menambahkan teks `Disetujui via sistem` pada kotak tanda tangan mahasiswa di `f1.blade.php`.
     - Menyesuaikan formatting nilai F2 ke 2 desimal: `number_format($case->final_score, 2)` dan sinkronisasi `$huruf_mutu`.
     - Mengubah label F5 menjadi `Rata-Rata Nilai Mentor`, nilai rata-rata menjadi `number_format($average, 2)`, dan catatan tanda tangan menjadi `(Tanda tangan asli terdapat pada dokumen sumber)`.
   - **Status:** **Lulus (4 passed, 18 assertions)**.

---

## 3. Hasil Eksekusi Lengkap PHPUnit Test Suite

Eksekusi menyeluruh terhadap seluruh modul pengujian aplikasi FORTA membuktikan kestabilan 100%:

```text
PASS  Tests\Unit\ExampleTest (1 test)
PASS  Tests\Feature\CalendarImportTest (1 test)
PASS  Tests\Feature\DashboardAndExportTest (3 tests)
PASS  Tests\Feature\DefenseCalculationTest (2 tests)
PASS  Tests\Feature\DefensePdfGeneratorTest (4 tests)
PASS  Tests\Feature\DynamicFormBuilderTest (3 tests)
PASS  Tests\Feature\ExampleTest (2 tests)
PASS  Tests\Feature\FormAndSubmissionReviewTest (4 tests)
PASS  Tests\Feature\FormManagerReorderTest (3 tests)
PASS  Tests\Feature\FormUploadSubmissionTest (2 tests)
PASS  Tests\Feature\InternshipLetterTest (2 tests)
PASS  Tests\Feature\LoginTest (2 tests)
PASS  Tests\Feature\MentorDocumentTest (1 test)
PASS  Tests\Feature\PublicCalendarTest (1 test)
PASS  Tests\Feature\SignatureRequestFormTest (4 tests)
PASS  Tests\Feature\StudentManagerTest (5 tests)
PASS  Tests\Feature\SyncOfficialStudentRosterTest (2 tests)

Tests:    42 passed (202 assertions)
Duration: 5.11s
```

---

## 4. Kesimpulan Akhir

1. **Seluruh Kegagalan Teratasi:** Seluruh bug, inkonsistensi skema basis data, masalah model obsoleter, kesalahan parameter layanan, hingga ketidaksesuaian tampilan dokumen PDF berhasil diperbaiki secara tuntas.
2. **Kesiapan Sistem:** Sistem Informasi FORTA (INFORTA) telah mencapai tingkat keandalan optimal, diverifikasi melalui 203 kasus uji black-box (100% Lulus) dan 42 tes integrasi otomatis (100% Lulus).
