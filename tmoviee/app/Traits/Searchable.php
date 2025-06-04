<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    /**
     * Apply search filters to the query
     *
     * @param Builder $query
     * @param string $search
     * @param array $searchableColumns
     * @return Builder
     */
    public function applySearch(Builder $query, string $search, array $searchableColumns): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($query) use ($search, $searchableColumns) {
            foreach ($searchableColumns as $column) {
                $query->orWhere($column, 'like', '%' . $search . '%');
            }
        });
    }

    /**
     * Get search results with pagination
     *
     * @param Builder $query
     * @param Request $request
     * @param array $searchableColumns
     * @param int $perPage
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSearchResults($query, $request, array $searchableColumns, int $perPage = 10)
    {
        $search = $request->input('search', '');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $query = $this->applySearch($query, $search, $searchableColumns);

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        // Get paginated results
        $results = $query->paginate($perPage);

        return response()->json([
            'data' => $results->items(),
            'total' => $results->total(),
            'per_page' => $results->perPage(),
            'current_page' => $results->currentPage(),
            'last_page' => $results->lastPage(),
            'search' => $search,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder
        ]);
    }
} 