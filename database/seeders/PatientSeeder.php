<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $patients = [
            [
                'full_name' => 'Test Patient',
                'email' => 'patient@smartclinic.com',
            ],
            [
                'full_name' => 'Alice Walker',
                'email' => 'alice.walker@smartclinic.com',
            ],
            [
                'full_name' => 'Mohamed Ali',
                'email' => 'mohamed.ali@smartclinic.com',
            ],
            [
                'full_name' => 'Fatima Noor',
                'email' => 'fatima.noor@smartclinic.com',
            ],
            [
                'full_name' => 'Carlos Ruiz',
                'email' => 'carlos.ruiz@smartclinic.com',
            ],
        ];

        foreach ($patients as $p) {
            User::create([
                'full_name' => $p['full_name'],
                'email' => $p['email'],
                'password' => Hash::make('password123'),
                'role' => 'patient',
            ]);
        }
    }
}
