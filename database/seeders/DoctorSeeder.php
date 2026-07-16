<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $specialties = Specialty::all();

        if ($specialties->isEmpty()) {
            return;
        }

        $doctorsData = [
            [
                'full_name' => 'Dr. John Smith',
                'email' => 'john.smith@smartclinic.com',
                'specialty' => 'Cardiology',
                'consultation_fee' => 250.00,
            ],
            [
                'full_name' => 'Dr. Sarah Johnson',
                'email' => 'sarah.johnson@smartclinic.com',
                'specialty' => 'Pediatrics',
                'consultation_fee' => 180.00,
            ],
            [
                'full_name' => 'Dr. Michael Brown',
                'email' => 'michael.brown@smartclinic.com',
                'specialty' => 'Dentist',
                'consultation_fee' => 150.00,
            ],
            [
                'full_name' => 'Dr. Emily Davis',
                'email' => 'emily.davis@smartclinic.com',
                'specialty' => 'Neurology',
                'consultation_fee' => 320.00,
            ],
            [
                'full_name' => 'Dr. Robert Wilson',
                'email' => 'robert.wilson@smartclinic.com',
                'specialty' => 'Orthopedics',
                'consultation_fee' => 200.00,
            ],
            [
                'full_name' => 'Dr. Lisa Anderson',
                'email' => 'lisa.anderson@smartclinic.com',
                'specialty' => 'Cardiology',
                'consultation_fee' => 275.00,
            ],
            [
                'full_name' => 'Dr. James Taylor',
                'email' => 'james.taylor@smartclinic.com',
                'specialty' => 'Pediatrics',
                'consultation_fee' => 190.00,
            ],
        ];

        foreach ($doctorsData as $data) {
            $specialty = $specialties->firstWhere('name', $data['specialty']);

            if (!$specialty) {
                continue;
            }

            $user = User::create([
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'password' => Hash::make('password123'),
                'role' => 'doctor',
            ]);

            Doctor::create([
                'user_id' => $user->id,
                'specialty_id' => $specialty->id,
                'consultation_fee' => $data['consultation_fee'],
            ]);
        }
    }
}
