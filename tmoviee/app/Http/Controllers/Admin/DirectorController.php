<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Director;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DirectorController extends Controller
{
    protected $searchableColumns = [
        'name',
        'biography',
        'birth_place'
    ];

    public function index(Request $request)
    {
        try {
            $query = Director::query();
            
            // Apply search if search parameter exists
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    foreach ($this->searchableColumns as $column) {
                        $q->orWhere($column, 'like', '%' . $search . '%');
                    }
                });
            }

            // Apply sorting
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Get paginated results
            $results = $query->paginate(10);

            return response()->json([
                'data' => $results->items(),
                'total' => $results->total(),
                'per_page' => $results->perPage(),
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'search' => $request->input('search', ''),
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder
            ]);
        } catch (\Exception $e) {
            Log::error('Director search error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Có lỗi xảy ra khi tìm kiếm đạo diễn',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:directors,slug',
            'biography' => 'nullable|string',
            'avatar' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string',
            'status' => 'boolean'
        ]);
        $director = Director::create($data);
        return response()->json($director, 201);
    }

    public function show(Director $director)
    {
        return response()->json($director);
    }

    public function update(Request $request, Director $director)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:directors,slug,' . $director->id,
            'biography' => 'nullable|string',
            'avatar' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string',
            'status' => 'boolean'
        ]);
        $director->update($data);
        return response()->json($director);
    }

    public function destroy(Director $director)
    {
        $director->delete();
        return response()->json(['message' => 'Đã xóa đạo diễn']);
    }
} 