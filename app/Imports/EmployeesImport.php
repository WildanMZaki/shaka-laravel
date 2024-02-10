<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class EmployeesImport implements ToModel, WithStartRow
{
    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        $access_id = [
            'Direktur' => 3,
            'HRD' => 4,
            'Team Leader' => 5,
            'SPG Freelancer' => 6,
            'SPG Training' => 7,
        ];
        $isDeveloper = auth()->user()->access_id == 1;
        $photo = $isDeveloper ? $row[5] : null;
        return new User([
            'name' => $row[0],
            'access_id' => $access_id[$row[4]],
            'phone' => $row[1],
            'email' => $row[2],
            'nik' => $row[3],
            'password' => 'Shakapratama',
            'photo' => $photo,
        ]);
    }
}
