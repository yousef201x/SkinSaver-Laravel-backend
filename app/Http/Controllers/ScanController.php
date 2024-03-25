<?php

namespace App\Http\Controllers;

use App\Http\Resources\ScanResource;
use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScanController extends Controller
{
    private function getScans($paginate = 10)
    {
        $scans = Scan::with('user')->paginate($paginate);
        return ScanResource::collection($scans);
    }
    private function getScanWhereUserName($name, $paginate = 10)
    {
        $scans = Scan::whereHas('user', function ($user) use ($name) {
            $user->where('name', 'like', '%' . $name . '%');
        })->with('user')->paginate($paginate);

        return ScanResource::collection($scans);
    }

    private function storeValidation(Request $request)
    {
        return $request->validate([
            'user_id' => 'required|exists:users,id',
            'image_path' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    }

    public function index(Request $request)
    {
        try {
            if ($request->search) {
                $name = $request->search;
                $scans = $this->getScanWhereUserName($name);
            } else {
                $scans = $this->getScans();
            }
            return response()->json(['data' => $scans], 200);
        } catch (\Exception $exception) {
            Log::error('ScanController@index Error: ' . $exception->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again later.'], 500);
        }
    }
}
