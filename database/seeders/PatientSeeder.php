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
                'full_name' => 'مريض تجريبي',
                'email' => 'patient@smartclinic.com',
            ],
            [
                'full_name' => 'أليس ووكر',
                'email' => 'alice.walker@smartclinic.com',
            ],
            [
                'full_name' => 'محمد علي',
                'email' => 'mohamed.ali@smartclinic.com',
            ],
            [
                'full_name' => 'فاطمة نور',
                'email' => 'fatima.noor@smartclinic.com',
            ],
            [
                'full_name' => 'كارلوس رويز',
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
