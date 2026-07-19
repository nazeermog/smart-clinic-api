<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentNotesRequest;
use App\Http\Requests\UpdateAppointmentStatusRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $patient = $request->user();

        $conflict = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->exists();

        if ($conflict) {
            return response()->json([
                'message' => 'This appointment time is already booked for the selected doctor.',
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'status' => 'pending',
        ]);

        $appointment->load(['patient', 'doctor.user', 'doctor.specialty']);

        return response()->json([
            'message' => 'Appointment booked successfully',
            'appointment' => AppointmentResource::make($appointment),
        ], JsonResponse::HTTP_CREATED);
    }

    public function patientAppointments(Request $request, ?int $id = null): JsonResponse
    {
        $user = $request->user();
        $patientId = $id ?? $user->id;

        if ($user->role !== 'patient' || $user->id !== $patientId) {
            return response()->json([
                'message' => 'You can only view your own appointments.',
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $appointments = Appointment::with(['doctor.user', 'doctor.specialty'])
            ->where('patient_id', $patientId)
            ->orderByDesc('appointment_date')
            ->get();

        $upcoming = $appointments->filter(
            fn (Appointment $a) => in_array($a->status, ['pending', 'confirmed'])
        )->values();

        $previous = $appointments->filter(
            fn (Appointment $a) => in_array($a->status, ['completed', 'cancelled'])
        )->values();

        return response()->json([
            'appointments' => AppointmentResource::collection($appointments),
            'upcoming' => AppointmentResource::collection($upcoming),
            'previous' => AppointmentResource::collection($previous),
        ]);
    }

    public function cancelAppointment(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'patient') {
            return response()->json([
                'message' => 'Only patients can cancel appointments.',
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $appointment = Appointment::where('patient_id', $user->id)->find($id);

        if (!$appointment) {
            return response()->json([
                'message' => 'Appointment not found',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        if (!in_array($appointment->status, ['pending', 'confirmed'], true)) {
            return response()->json([
                'message' => 'Only pending or confirmed appointments can be cancelled.',
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $appointment->update(['status' => 'cancelled']);
        $appointment->load(['patient', 'doctor.user', 'doctor.specialty']);

        return response()->json([
            'message' => 'Appointment cancelled successfully',
            'appointment' => AppointmentResource::make($appointment),
        ]);
    }

    public function doctorTodayAppointments(Request $request, int $doctorId = null): JsonResponse
    {
        $doctor = $this->getDoctor($request, $doctorId);

        if (!$doctor) {
            return $this->error('Doctor profile not found', [], JsonResponse::HTTP_NOT_FOUND);
        }

        $appointments = Appointment::with(['patient', 'doctor.user', 'doctor.specialty'])
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_date')
            ->get();

        return response()->json([
            'appointments' => AppointmentResource::collection($appointments),
        ]);
    }

    public function doctorAllAppointments(Request $request, int $doctorId = null): JsonResponse
    {
        $doctor = $this->getDoctor($request, $doctorId);

        if (!$doctor) {
            return $this->error('Doctor profile not found', [], JsonResponse::HTTP_NOT_FOUND);
        }

        $appointments = Appointment::with(['patient', 'doctor.user', 'doctor.specialty'])
            ->where('doctor_id', $doctor->id)
            ->orderByDesc('appointment_date')
            ->get();

        return response()->json([
            'appointments' => AppointmentResource::collection($appointments),
        ]);
    }

    public function updateStatus(UpdateAppointmentStatusRequest $request, int $id): JsonResponse
    {
        $doctor = $this->getDoctor($request);

        if (!$doctor) {
            return response()->json([
                'message' => 'Doctor profile not found',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $appointment = Appointment::where('doctor_id', $doctor->id)->find($id);

        if (!$appointment) {
            return response()->json([
                'message' => 'Appointment not found',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $appointment->update(['status' => $request->status]);

        $appointment->load(['patient', 'doctor.user', 'doctor.specialty']);

        return response()->json([
            'message' => 'Appointment status updated successfully',
            'appointment' => AppointmentResource::make($appointment),
        ]);
    }

    public function updateNotes(UpdateAppointmentNotesRequest $request, int $id): JsonResponse
    {
        $doctor = $this->getDoctor($request);

        if (!$doctor) {
            return response()->json([
                'message' => 'Doctor profile not found',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $appointment = Appointment::where('doctor_id', $doctor->id)->find($id);

        if (!$appointment) {
            return response()->json([
                'message' => 'Appointment not found',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $appointment->update(['doctor_notes' => $request->doctor_notes]);

        $appointment->load(['patient', 'doctor.user', 'doctor.specialty']);

        return response()->json([
            'message' => 'Medical notes updated successfully',
            'appointment' => AppointmentResource::make($appointment),
        ]);
    }

    private function getDoctor(Request $request, ?int $doctorId = null): ?Doctor
    {
        $user = $request->user();

        if ($doctorId !== null) {
            if ($user->role !== 'doctor') {
                return null;
            }

            $doctor = Doctor::where('id', $doctorId)->first();

            return $doctor && $doctor->user_id === $user->id ? $doctor : null;
        }

        return Doctor::where('user_id', $user->id)->first();
    }
}
