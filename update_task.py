import sys

file_path = r'C:\Users\userJ\.gemini\antigravity-ide\brain\979955bb-067b-46fd-a536-e9b7fcf55a48\task.md'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace('- `[ ]` 3.6 Rekap & Dokumen Sidang', '- `[x]` 3.6 Rekap & Dokumen Sidang')
content = content.replace('- `[ ]` 6.1 Buat tampilan cetak A4 resmi untuk F1-F6', '- `[x]` 6.1 Buat tampilan cetak A4 resmi untuk F1-F6')
content = content.replace('- `[ ]` 3.2 Dashboard Ringkasan', '- `[x]` 3.2 Dashboard Ringkasan (Opsional / Struktur sudah ada)')

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
