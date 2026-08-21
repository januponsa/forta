<x-mail::message>
# Halo, {{ $request->student->name }}

Status permohonan surat pengantar magang/kerja praktik Anda ke perusahaan **{{ $request->company_name }}** telah diperbarui.

**Status Saat Ini:** {{ $statusLabel }}

@if($request->status === 'revision_required')
<x-mail::panel>
**Catatan Revisi dari Admin:**
{{ $note ?? $request->revision_note }}
</x-mail::panel>
Silakan login ke portal FORTA dan perbaiki permohonan Anda.

<x-mail::button :url="route('student.internship-letters.edit', $request->id)">
Perbaiki Permohonan
</x-mail::button>
@elseif($request->status === 'rejected')
<x-mail::panel>
**Alasan Penolakan:**
{{ $note ?? $request->rejection_reason }}
</x-mail::panel>
@elseif(in_array($request->status, ['approved', 'generated', 'completed']))
Permohonan Anda telah disetujui. 
@if($request->letter_number)
**No Surat:** {{ $request->letter_number }}
@endif

Anda dapat mengunduh surat resmi dalam bentuk PDF di portal.

<x-mail::button :url="route('student.internship-letters.index')">
Lihat Surat
</x-mail::button>
@endif

Terima kasih,<br>
Admin {{ config('app.name') }}
</x-mail::message>
