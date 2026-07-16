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
        $doctors = Doctor::all();
        $patients = User::where('role', 'patient')->get();

        if ($doctors->isEmpty() || $patients->isEmpty()) {
            return;
        }

        // Create some sample appointments for each doctor
        foreach ($doctors as $doctor) {
            // Today's appointment
            Appointment::create([
                'patient_id' => $patients->random()->id,
                'doctor_id' => $doctor->id,
                'appointment_date' => now()->setHour(10)->setMinute(0)->setSecond(0),
                'status' => 'confirmed',
            ]);

            Appointment::create([
                'patient_id' => $patients->random()->id,
                'doctor_id' => $doctor->id,
                'appointment_date' => now()->setHour(14)->setMinute(30)->setSecond(0),
                'status' => 'pending',
            ]);

            // Upcoming appointment
            Appointment::create([
                'patient_id' => $patients->random()->id,
                'doctor_id' => $doctor->id,
                'appointment_date' => now()->addDays(3)->setHour(11)->setMinute(0)->setSecond(0),
                'status' => 'pending',
            ]);

            // Completed appointment with notes
            Appointment::create([
                'patient_id' => $patients->random()->id,
                'doctor_id' => $doctor->id,
                'appointment_date' => now()->subDays(7)->setHour(9)->setMinute(0)->setSecond(0),
                'status' => 'completed',
                'doctor_notes' => 'Patient examined. Prescribed medication for 7 days. Follow-up recommended in 2 weeks.',
            ]);

            // Cancelled appointment
            Appointment::create([
                'patient_id' => $patients->random()->id,
                'doctor_id' => $doctor->id,
                'appointment_date' => now()->subDays(2)->setHour(15)->setMinute(0)->setSecond(0),
                'status' => 'cancelled',
            ]);
        }
    }
}
