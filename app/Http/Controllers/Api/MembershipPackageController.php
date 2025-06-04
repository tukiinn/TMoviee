<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MembershipPackage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MembershipPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $packages = MembershipPackage::where('is_active', true)->get();
        return response()->json($packages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        $package = MembershipPackage::create($validated);
        return response()->json($package, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(MembershipPackage $membershipPackage): JsonResponse
    {
        return response()->json($membershipPackage);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MembershipPackage $membershipPackage): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'price' => 'numeric|min:0',
            'duration_days' => 'integer|min:1',
            'features' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        $membershipPackage->update($validated);
        return response()->json($membershipPackage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MembershipPackage $membershipPackage): JsonResponse
    {
        $membershipPackage->delete();
        return response()->json(null, 204);
    }
}
