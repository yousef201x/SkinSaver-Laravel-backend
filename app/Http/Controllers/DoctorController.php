<?php

namespace App\Http\Controllers;

use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    // Function to fetch doctors based on name with pagination
    private function getDoctorWhereName($name, $paginate = 10)
    {
        return $doctors = Doctor::where('doctors.name', 'like', '%' . $name . '%')->orderBy('id', 'desc')->paginate($paginate);
    }

    // Validation rules for storing a new doctor
    private function storeValidation(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|max:255|unique:doctors,email',
            'phone_number' => 'required|numeric|unique:doctors,phone_number|digits:11',
            'clinic_address' => 'required|max:255',
            'schedule' => 'required|max:255',
            'doctor_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
    }

    // Validation rules for updating an existing doctor
    private function updateValidation(Request $request)
    {
        $request->validate([
            'name' => 'nullable|max:255',
            'email' => 'nullable|max:255|unique:doctors,email',
            'phone_number' => 'nullable|numeric|digits:11',
            'clinic_address' => 'nullable|max:255',
            'schedule' => 'nullable|max:255',
            'doctor_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
    }

    // Get doctors data with optional search and pagination
    public function index(Request $request)
    {
        try {
            if ($request->search) {
                // If search parameter is provided, filter doctors by name
                $search = $request->search;
                $doctors = $this->getDoctorWhereName($search);
            } else {
                // Fetch all doctors with default pagination
                $doctors = Doctor::select('doctors.*')->orderBy(
                    'id',
                    'desc'
                )->paginate(10);
            }

            // Transform the doctors data into the DoctorResource format
            $doctorsResource = DoctorResource::collection($doctors);

            // Return doctors data as JSON response using the resource
            return $doctorsResource;
        } catch (\Exception $exception) {
            // Log any errors and return an error response
            Log::error('DoctorController@index Error: ' . $exception->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again later.'], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            // Find the doctor by ID
            $doctor = Doctor::findOrFail($id);

            // Check if the doctor was found
            if (!$doctor) {
                // Return error response if doctor is not found
                return response()->json(['message' => 'Doctor not found'], 404);
            }

            // Transform the doctor data into the DoctorResource format
            $doctorResource = new DoctorResource($doctor);

            // Return doctor data as JSON response using the resource
            return $doctorResource;
        } catch (\Exception $exception) {
            // Log any errors and return an error response
            Log::error('DoctorController@show Error: ' . $exception->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again later.'], 500);
        }
    }


    public function store(Request $request)
    {
        // Validate the request data
        $this->storeValidation($request);

        // Handle doctor image upload
        if ($request->file('doctor_image')) {
            $imagePath = $request->file('doctor_image')->store('images');
        } else {
            // Return error response if doctor image is not provided
            return response()->json(['message' => 'Doctor image is required'], 500);
        }

        try {
            // Create a new doctor instance and save it to the database
            $doctor = new Doctor();
            $doctor->name = $request->name;
            $doctor->email = $request->email;
            $doctor->phone_number = $request->phone_number;
            $doctor->clinic_address = $request->clinic_address;
            $doctor->schedule = $request->schedule;
            $doctor->doctor_image = $imagePath; // Assign the image path to the property
            $doctor->save();

            // Return success response if doctor is saved successfully
            return response()->json(['message' => 'Doctor created successfully'], 200);
        } catch (\Exception $exception) {
            // Log any errors and return an error response
            Log::error('DoctorController@index Error: ' . $exception->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again later.' . $exception->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $doctor = Doctor::find($id);
        return view('test')->with(compact('doctor'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request data
        $this->updateValidation($request);
        try {
            // Find the doctor by ID
            $doctor = Doctor::findOrFail($id);

            if ($doctor) {

                // Handle doctor image upload if provided
                if ($request->file('doctor_image'))
                    if ($doctor->doctor_image) {
                        // Delete the previous image if it exists
                        Storage::delete($doctor->doctor_image);
                        // Store the new image
                        $imagePath = $request->file('doctor_image')->store('images');
                        // Update the doctor with the new image path
                        $doctor->update([
                            'doctor_image' => $imagePath
                        ]);
                    }
                // Update other fields of the doctor if provided
                $doctor->update($request->except('doctor_image'));
            } else {
                return response()->json(['message' => 'Doctor not found'], 404);
            }

            // Return success response
            return response()->json(['message' => 'Doctor updated successfully'], 200);
        } catch (\Exception $exception) {
            // Log any errors and return an error response
            Log::error('DoctorController@update Error: ' . $exception->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again later.' . $exception->getMessage()], 500);
        }
    }

    // Destroy (delete) a doctor record
    public function destroy($id)
    {
        try {
            // Find the doctor by ID
            $doctor = Doctor::findOrFail($id);

            // Check if the doctor has an associated image and delete it
            if ($doctor->doctor_image) {
                Storage::delete($doctor->doctor_image);
            }

            // Delete the doctor record
            $doctor->delete();

            // Return success response
            return response()->json(['message' => 'Doctor deleted successfully'], 200);
        } catch (\Exception $exception) {
            // Log any errors and return an error response
            Log::error('DoctorController@destroy Error: ' . $exception->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again later.' . $exception->getMessage()], 500);
        }
    }
}
