<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipPackage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MembershipPackageController extends Controller
{
    public function index(): JsonResponse
    {
        $packages = MembershipPackage::all();
        return response()->json($packages);
    }

    public function store(Request $request): JsonResponse
    {
        if ($request->has('features') && is_string($request->features)) {
            $request->merge([
                'features' => json_decode($request->features, true) ?? []
            ]);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = uniqid('membership_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/membership'), $filename);
            $validated['image'] = '/images/membership/' . $filename;
        }

        $package = MembershipPackage::create($validated);
        return response()->json($package, 201);
    }

    public function show(MembershipPackage $membershipPackage): JsonResponse
    {
        return response()->json($membershipPackage);
    }

    public function update(Request $request, MembershipPackage $membershipPackage): JsonResponse
    {
        if ($request->has('features') && is_string($request->features)) {
            $request->merge([
                'features' => json_decode($request->features, true) ?? []
            ]);
        }
        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'price' => 'numeric|min:0',
            'duration_days' => 'integer|min:1',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = uniqid('membership_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/membership'), $filename);
            $validated['image'] = '/images/membership/' . $filename;
        }

        $membershipPackage->update($validated);
        return response()->json($membershipPackage);
    }

    public function destroy(MembershipPackage $membershipPackage): JsonResponse
    {
        $membershipPackage->delete();
        return response()->json(null, 204);
    }
}
