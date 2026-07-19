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
                'full_name' => 'د. أحمد الشامي',
                'email' => 'ahmed.shami@smartclinic.com',
                'specialty' => 'طب القلب',
                'consultation_fee' => 250.00,
            ],
            [
                'full_name' => 'د. سارة الحمود',
                'email' => 'sara.hamoud@smartclinic.com',
                'specialty' => 'طب الأطفال',
                'consultation_fee' => 180.00,
            ],
            [
                'full_name' => 'د. محمد العلي',
                'email' => 'mohammad.ali@smartclinic.com',
                'specialty' => 'طب الأسنان',
                'consultation_fee' => 150.00,
            ],
            [
                'full_name' => 'د. ريم القيسي',
                'email' => 'reem.qaisi@smartclinic.com',
                'specialty' => 'طب الأعصاب',
                'consultation_fee' => 320.00,
            ],
            [
                'full_name' => 'د. خالد المدني',
                'email' => 'khaled.almadani@smartclinic.com',
                'specialty' => 'جراحة العظام',
                'consultation_fee' => 200.00,
            ],
            [
                'full_name' => 'د. لين أحمد',
                'email' => 'leen.ahmed@smartclinic.com',
                'specialty' => 'طب القلب',
                'consultation_fee' => 275.00,
            ],
            [
                'full_name' => 'د. ياسر فهد',
                'email' => 'yasser.fahd@smartclinic.com',
                'specialty' => 'طب الأطفال',
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
