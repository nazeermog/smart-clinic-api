<?php

namespace App\Http\Controllers;

use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Doctor::with(['user', 'specialty']);

        if ($request->has('specialty_id')) {
            $query->where('specialty_id', $request->integer('specialty_id'));
        }

        if ($request->has('specialty')) {
            $query->whereHas('specialty', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->string('specialty') . '%');
            });
        }

        if ($request->has('name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->string('name') . '%');
            });
        }

        $doctors = $query->orderByDesc('id')->get();

        return response()->json([
            'data' => DoctorResource::collection($doctors),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $doctor = Doctor::with(['user', 'specialty'])->find($id);

        if (!$doctor) {
            return response()->json([
                'message' => 'Doctor not found',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => DoctorResource::make($doctor),
        ]);
    }
}
