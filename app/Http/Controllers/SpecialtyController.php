<?php

namespace App\Http\Controllers;

use App\Http\Resources\SpecialtyResource;
use App\Models\Specialty;
use Illuminate\Http\JsonResponse;

class SpecialtyController extends Controller
{
    public function index(): JsonResponse
    {
        $specialties = Specialty::withCount('doctors')->get();

        return response()->json([
            'data' => SpecialtyResource::collection($specialties),
        ]);
    }
}
