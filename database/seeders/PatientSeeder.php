<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'full_name' => 'Test Patient',
            'email' => 'patient@smartclinic.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
        ]);

        User::factory()
            ->count(5)
            ->create([
                'role' => 'patient',
            ]);
    }
}
