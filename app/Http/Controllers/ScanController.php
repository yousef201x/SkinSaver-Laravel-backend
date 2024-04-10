<?php

namespace App\Http\Controllers;

use App\Http\Resources\ScanResource;
use App\Models\Scan;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ScanController extends Controller
{
    // Retrieve scans with pagination
    private function getScans($paginate = 10)
    {
        // Retrieve scans from the database along with associated user data and paginate the results
        $scans = Scan::with('user')->paginate($paginate);
        // Convert the scanned data into a resource collection for JSON response
        return ScanResource::collection($scans);
    }

    // Retrieve scans based on user name with pagination
    private function getScanWhereUserName($name, $paginate = 10)
    {
        // Query scans that have a user with a name containing the provided search string
        $scans = Scan::whereHas('user', function ($user) use ($name) {
            $user->where('name', 'like', '%' . $name . '%');
        })->with('user')->paginate($paginate);

        // Convert the scanned data into a resource collection for JSON response
        return ScanResource::collection($scans);
    }

    // Validate store request data
    private function storeValidation(Request $request)
    {
        // Validate that the request contains an 'image_path' field with an image file and specific criteria
        return $request->validate([
            'image_path' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    }

    // Handle index endpoint for listing scans
    public function index(Request $request)
    {
        try {
            // Check if a search query parameter is present in the request
            if ($request->search) {
                $name = $request->search;
                // Fetch scans filtered by user name using the getScanWhereUserName method
                $scans = $this->getScanWhereUserName($name);
            } else {
                // Fetch all scans using the getScans method
                $scans = $this->getScans();
            }
            // Return a JSON response containing the scan data
            return response()->json(['data' => $scans], 200);
        } catch (\Exception $exception) {
            // Log any exceptions and return a server error response
            Log::error('ScanController@index Error: ' . $exception->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again later.'], 500);
        }
    }

    // Handle store endpoint for creating new scans
    public function store(Request $request)
    {
        // Validate the request data using the storeValidation method
        $this->storeValidation($request);
        try {
            // Retrieve the authenticated user's ID
            $userId = auth()->id();
            // Check if an image file is present in the request
            if ($request->file('image_path')) {
                // Store the image file and create a new Scan instance
                $path = $request->file('image_path')->store('scans');
                $scan = new Scan();
                $scan->user_id = $userId;
                $scan->image_path = $path;
                $scan->save();
                // Return a success message as a JSON response
                return response()->json(['message' => 'Scan created successfully'], 500);
            } else {
                // Return a JSON response indicating that a scan image is required
                return response()->json(['message' => 'scan image is required'], 500);
            }
        } catch (\Exception $exception) {
            // Log any exceptions and return a server error response
            Log::error('ScanController@index Error: ' . $exception->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again later.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Attempt to find the scan with the provided ID; throw an exception if not found
            $scan = Scan::findOrFail($id);

            if ($scan) {
                // If the scan is found, delete its associated image file from storage
                Storage::delete($scan->image_path);

                // Delete the scan record from the database
                $scan->delete();

                // Return a success message as a JSON response
                return response()->json(['message' => 'Scan Deleted'], 200);
            } else {
                // If the scan is not found, return a JSON response with a 404 status code
                return response()->json(['message' => 'Scan not found'], 404);
            }
        } catch (\Exception $exception) {
            // Log any exceptions and return a server error response
            Log::error('ScanController@index Error: ' . $exception->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again later.'], 500);
        }
    }
}
