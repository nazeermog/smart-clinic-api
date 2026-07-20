<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = Doctor::with('user')->get();
        $patients = User::where('role', 'patient')->get();

        if ($doctors->isEmpty() || $patients->isEmpty()) {
            return;
        }

        foreach ($doctors as $doctor) {
            $patientIds = $patients->pluck('id')->all();

            Appointment::firstOrCreate(
                [
                    'doctor_id' => $doctor->id,
                    'appointment_date' => now()->setHour(10)->setMinute(0)->setSecond(0),
                ],
                [
                    'patient_id' => $patientIds[array_rand($patientIds)],
                    'status' => 'confirmed',
                ]
            );

            Appointment::firstOrCreate(
                [
                    'doctor_id' => $doctor->id,
                    'appointment_date' => now()->setHour(14)->setMinute(30)->setSecond(0),
                ],
                [
                    'patient_id' => $patientIds[array_rand($patientIds)],
                    'status' => 'pending',
                ]
            );

            Appointment::firstOrCreate(
                [
                    'doctor_id' => $doctor->id,
                    'appointment_date' => now()->addDays(3)->setHour(11)->setMinute(0)->setSecond(0),
                ],
                [
                    'patient_id' => $patientIds[array_rand($patientIds)],
                    'status' => 'pending',
                ]
            );

            Appointment::firstOrCreate(
                [
                    'doctor_id' => $doctor->id,
                    'appointment_date' => now()->subDays(7)->setHour(9)->setMinute(0)->setSecond(0),
                ],
                [
                    'patient_id' => $patientIds[array_rand($patientIds)],
                    'status' => 'completed',
                    'doctor_notes' => 'تم فحص المريض. تم وصف العلاج لمدة 7 أيام. يُنصح بمتابعة خلال أسبوعين.',
                ]
            );

            Appointment::firstOrCreate(
                [
                    'doctor_id' => $doctor->id,
                    'appointment_date' => now()->subDays(2)->setHour(15)->setMinute(0)->setSecond(0),
                ],
                [
                    'patient_id' => $patientIds[array_rand($patientIds)],
                    'status' => 'cancelled',
                ]
            );
        }
    }
}
