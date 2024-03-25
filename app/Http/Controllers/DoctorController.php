<?php

namespace App\Http\Controllers;

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
            'phone_number' => 'required|numeric|digits:11',
            'clinic_address' => 'required|max:255',
            'schedule' => 'required|max:255',
            'doctor_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
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
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if ($request->search) {
                // If search parameter is provided, filter doctors by name
                $search = $request->search;
                $doctors = $this->getDoctorWhereName($search);
            } else {
                // Fetch all doctors with default pagination
                $doctors = Doctor::select('doctors.*')->orderBy('id', 'desc')->paginate(10);
            }
            // Return doctors data as JSON response
            return response()->json(['data' => $doctors], 200);
        } catch (\Exception $exception) {
            // Log any errors and return an error response
            Log::error('DoctorController@index Error: ' . $exception->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again later.'], 500);
        }
    }

    // Store a new doctor record
    public function store(Request $request)
    {
        // Validate the request data
        $validatedRequest = $this->storeValidation($request);

        // Handle doctor image upload
        if ($request->file('doctor_image')) {
            $imagePath = $request->file('doctor_image')->store('images');
        } else {
            // Return error response if doctor image is not provided
            return response()->json(['message' => 'doctor image is required'], 500);
        }

        try {
            // Create a new doctor instance and save it to the database
            $doctor = new Doctor();
            $doctor->name = $request->name;
            $doctor->email = $request->email;
            $doctor->phone_number = $request->phone_number;
            $doctor->clinic_address = $request->clinic_address;
            $doctor->schedule = $request->schedule;
            $doctor->doctor_image = $imagePath;
            $doctor->save();

            // Return success response if doctor is saved successfully
            return response()->json(['message' => 'doctor created successfully'], 200);
        } catch (\Exception $exception) {
            // Log any errors and return an error response
            Log::error('DoctorController@index Error: ' . $exception->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again later.' . $exception->getMessage()], 500);
        }
    }

    // Update an existing doctor record
    public function update(Request $request, $id)
    {
        // Validate the request data
        $this->updateValidation($request);

        try {
            // Find the doctor by ID
            $doctor = Doctor::findOrFail($id);

            // Handle doctor image upload if provided
            if ($request->file('doctor_image')) {
                // Delete the old image if it exists
                if ($doctor->doctor_image) {
                    Storage::delete($doctor->doctor_image);
                }

                // Store the new image and update the image path
                $imagePath = $request->file('doctor_image')->store('images');
                $doctor->doctor_image = $imagePath;
            }

            // Update the doctor's fields using mass assignment
            $doctor->update($request->except('doctor_image')); // Exclude the image field from mass assignment

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
