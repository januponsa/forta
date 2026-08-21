<?php
$sub = new \App\Models\Submission();
$sub->form_id = 10;
$sub->nim = '2023012345';
$sub->name = 'Januponsa Dio Firizqi';
$sub->email = 'student@example.com';
$sub->status = 'approved';
$sub->submitted_at = now();
$sub->answers = ['dummy' => 'data'];
$sub->save();
echo "Created dummy approved submission\n";
