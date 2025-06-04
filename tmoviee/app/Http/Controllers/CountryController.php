<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CountryController extends Controller
{
    public function index()
    {
        return Country::where('status', true)->get();
    }

    public function show($slug)
    {
        try {
            $country = Country::where('slug', $slug)
                ->where('status', true)
                ->firstOrFail();
            
            return response()->json($country);
        } catch (\Exception $e) {
            Log::error('Error fetching country:', [
                'slug' => $slug,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Không tìm thấy quốc gia'
            ], 404);
        }
    }
} 