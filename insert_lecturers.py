<?php

use App\Models\Lecturer;

$names = [
    "Dr. Eng. Handri Santoso, S.Si., M.Eng.",
    "Dr. Haryono, S.Kom., M.Eng.",
    "Erick Dazki, S.Kom, M.Kom.",
    "Januponsa Dio Firzqi, S.Kom., M.Kom.",
    "Alfa Ryano Yohannis, S.T., M.T., Ph.D.",
    "Dr. Theresia Herlina, S.Kom., M.T.",
    "Arya Sanjaya, S.T, M.Kom.",
    "Master Edison, S,Kom., M.Kom.",
    "Refgiufi Patria Avrianto, S.Kom., M.Kom."
];

foreach ($names as $name) {
    // Generate a dummy nip
    $nip = '10' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
    $email = strtolower(str_replace(' ', '.', preg_replace('/[^A-Za-z0-9 ]/', '', explode(',', $name)[0]))) . '@lecturer.forta.ac.id';
    
    // Check if exists
    $existing = Lecturer::where('name', $name)->first();
    if (!$existing) {
        Lecturer::create([
            'nip' => $nip,
            'name' => $name,
            'email' => $email,
            'is_active' => true
        ]);
        echo "Inserted: $name\n";
    } else {
        echo "Already exists: $name\n";
    }
}
