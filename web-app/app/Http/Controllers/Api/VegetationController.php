<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vegetation;
use Illuminate\Http\JsonResponse;

/**
 * Class VegetationController
 *
 * Provides the public API endpoint for the Digital Twin map-o.
 */
class VegetationController extends Controller
{
    /**
     * Return all (non-deleted) vegetation as a JSON array suitable for the Digital Twin map-o.
     *
     * GET /api/vegetation
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $vegetations = Vegetation::with(['species', 'species.type', 'area'])
            ->get();

        $data = $vegetations->map(function (Vegetation $vegetation) {
            $location   = $vegetation->location ?? [];
            $species    = $vegetation->species;
            $dutchName  = $species?->dutch_name;

            $grid = null;

            if (isset($location['x']) && isset($location['y'])) {
                // Convert x and y to a grid reference (e.g., "52.123, 5.456").
                $grid = number_format($location['x'], 3) . ', ' . number_format($location['y'], 3);
            }

            // Blossom months are stored as a JSON array; join them for the API.
            $blossomMonths = $species?->blossom_month;
            $bloei = $blossomMonths && count($blossomMonths) > 0
                ? implode(', ', $blossomMonths)
                : null;

            // The API label combines the Dutch plant name with the grid reference.
            $label = ($dutchName && $grid)
                ? $dutchName . '-' . str_replace(', ', '-', $grid)
                : null;

            return [
                'id'    => $vegetation->number,
                'name'  => $dutchName,
                'latin' => $species?->latin_name,
                'type'  => $species?->type?->name,
                'x'     => isset($location['x']) ? (float) $location['x'] : null,
                'y'     => isset($location['y']) ? (float) $location['y'] : null,
                'grid'  => $grid,
                'year'  => $vegetation->placed !== null ? (int) $vegetation->placed : null,
                'maxH'  => $species?->height !== null ? (float) $species->height : null,
                'bloei' => $bloei,
                'area'  => $vegetation->area?->name,
                'label' => $label,
                'notes' => $vegetation->remarks,
                'url'   => route('public.vegetation.show', ['vegetation' => $vegetation->uuid]),
            ];
        });

        return response()->json($data);
    }
}

