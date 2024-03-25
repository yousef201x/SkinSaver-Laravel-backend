<?php

namespace App\Http\Controllers;

use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactUsController extends Controller
{
    private function getMessagesWhereName($name, $paginate = 25)
    {
        return ContactUs::where('name', 'like', '%' . $name . '%')->paginate($paginate);
    }

    private function getMessagesWhereEmail($email, $paginate = 25)
    {
        return ContactUs::where('email', 'like', '%' . $email . '%')->paginate($paginate);
    }

    private function storeValidation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|exists:users,email',
            'message' => 'required|max:255',
        ]);
    }

    private function updateValidation(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|exists:users,email',
            'message' => 'nullable|max:255',
        ]);
    }

    public function index(Request $request)
    {
        try {
            if ($request->search) {
                // If search parameter is provided, filter ContactUs by name
                $search = $request->search;
                $ContactUs = $this->getMessagesWhereName($search);
            } else {
                // Fetch all ContactUs with default pagination
                $ContactUs = ContactUs::select('ContactUss.*')->orderBy('id', 'desc')->paginate(10);
            }
            // Return ContactUs data as JSON response
            return response()->json(['data' => $ContactUs], 200);
        } catch (\Exception $exception) {
            // Log any errors and return an error response
            Log::error('ContactUsController@index Error: ' . $exception->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again later.'], 500);
        }
    }

    public function store(Request $request)
    {
        $this->storeValidation($request);
        try {
            // Create a new ContactUs instance and save it to the database
            $ContactUs = new ContactUs();
            $ContactUs->name = $request->name;
            $ContactUs->email = $request->email;
            $ContactUs->message = $request->message;
            $ContactUs->save();

            // Return success response if ContactUs is saved successfully
            return response()->json(['message' => 'Message sent successfully'], 200);
        } catch (\Exception $exception) {
            // Log any errors and return an error response
            Log::error('ContactUsController@store Error: ' . $exception->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again later.' . $exception->getMessage()], 500);
        }
    }
    public function update(Request $request, $id)
    {
        $this->updateValidation($request);

        try {
            // Find the ContactUs message by ID
            $ContactUs = ContactUs::findOrFail($id);

            // Update the message fields using mass assignment
            $ContactUs->update($request->all());

            // Return success response if ContactUs is updated successfully
            return response()->json(['message' => 'Message updated successfully'], 200);
        } catch (\Exception $exception) {
            // Log any errors and return an error response
            Log::error('ContactUsController@update Error: ' . $exception->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again later.' . $exception->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Find the ContactUs message by ID and delete
            $ContactUs = ContactUs::findOrFail($id);
            $ContactUs->delete();

            // Return success response
            return response()->json(['message' => 'Message deleted successfully'], 200);
        } catch (\Exception $exception) {
            // Log any errors and return an error response
            Log::error('ContactUsController@destroy Error: ' . $exception->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again later.' . $exception->getMessage()], 500);
        }
    }
}
