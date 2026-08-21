<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentImport implements ToModel, WithHeadingRow
{
    /**
     * @return Model|null
     */
    public function model(array $row)
    {
        return Student::updateOrCreate(
            ['nim' => $row['nim']],
            [
                'name' => $row['nama'],
                'email' => $row['email'],
                'angkatan' => $row['angkatan'],
            ]
        );
    }
}
