<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DoctorController extends Controller
{
    private function getDoctorWhereName($name,$paginate=10)
    {
        return $doctors = Doctor::where('doctors.name','like','%'.$name.'%')->orderBy('id', 'desc')->paginate($paginate);
    }

    private function storeValidation(Request $request)
    {
        $request->validate([
            'name'=>'required|max:255',
            'email'=>'required|max:255|unique:doctors,email',
            'phone_number'=>'required|numeric|digits:11',
            'clinic_address'=>'required|max:255',
            'schedule'=>'required|max:255',
            'doctor_image'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
    }

    private function updateValidation(Request $request)
    {
        $request->validate([
            'name'=>'nullable|max:255',
            'email'=>'nullable|max:255|unique:doctors,email',
            'phone_number'=>'nullable|numeric|digits:11',
            'clinic_address'=>'nullable|max:255',
            'schedule'=>'nullable|max:255',
            'doctor_image'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
    }
    public function index(Request $request, $paginate = 10): \Illuminate\Http\JsonResponse
    {
        try {
            if ($request->search) {
                $search = $request->search;
                $doctors = $this->getDoctorWhereName($search);
            } else {
                $doctors = Doctor::select('doctors.*')->orderBy('id', 'desc')->paginate($paginate);
            }
            return response()->json(['data' => $doctors], 200);
        } catch (\Exception $exception) {
            Log::error('DoctorController@index Error: ' . $exception->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again later.'], 500);
        }
    }


}
