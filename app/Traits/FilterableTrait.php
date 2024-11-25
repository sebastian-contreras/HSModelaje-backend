<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait FilterableTrait
{
    private function applyFilters($query, $filters)
    {
        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                $query->where($key, 'LIKE', '%' . $value . '%');
            }
        }
    }

    private function applySorting($query, Request $request, array $validSortFields = [], array $validSortDirections = ['asc', 'desc'], $defaultSortField = 'IdPersona', $defaultSortDirection = 'desc')
    {
        // Si no se proporcionan campos válidos, se usan los predeterminados

        $sortField = in_array($request->input('sort_by'), $validSortFields) ? $request->input('sort_by') : $defaultSortField;
        $sortDirection = in_array($request->input('sort_direction'), $validSortDirections) ? $request->input('sort_direction') : $defaultSortDirection;

        $query->orderBy($sortField, $sortDirection);
    }
}