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

    public function store(Request $request) : \Illuminate\Http\JsonResponse
    {
        $this->storeValidation($request);
        try{
            if ($request->hasFile('doctor_image')) {
                $image = $request->file('doctor_image');
                $imagePath = $image->store('public/images');

                // Create a new Doctor record
                $doctor = new Doctor();
                $doctor->name = $request->name;
                $doctor->email = $request->email;
                $doctor->phone_number = $request->phone_number;
                $doctor->clinic_address = $request->clinic_address;
                $doctor->schedule = $request->schedule;
                $doctor->doctor_image = $imagePath;
                $doctor->save();

                return response()->json(['message' => 'Doctor created successfully.', 'data' => $doctor], 201);
            } else {
                return response()->json(['message' => 'Doctor image is required.'], 400);
            }
        } catch (\Exception $exception) {
            Log::error('DoctorController@store Error: ' . $exception->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again later.'], 500);
        }
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $this->updateValidation($request); // Validate request data

        try {
            $doctor = Doctor::find($id);

            if (!$doctor) {
                return response()->json(['message' => 'Doctor not found.'], 404);
            }

            $doctor->update($request->all());

            if ($request->hasFile('doctor_image')) {
                // Update doctor's image if a new image is uploaded
                $image = $request->file('doctor_image');
                $imagePath = $image->store('public/images');
                $doctor->doctor_image = $imagePath;
            }

            $doctor->save();

            return response()->json(['message' => 'Doctor updated successfully.', 'data' => $doctor], 200);
        } catch (\Exception $exception) {
            Log::error('DoctorController@update Error: ' . $exception->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again later.'], 500);
        }
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        try {
            $doctor = Doctor::find($id);

            if (!$doctor) {
                return response()->json(['message' => 'Doctor not found.'], 404);
            }

            $doctor->delete();

            return response()->json(['message' => 'Doctor deleted successfully.'], 200);
        } catch (\Exception $exception) {
            Log::error('DoctorController@destroy Error: ' . $exception->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again later.'], 500);
        }
    }

}
