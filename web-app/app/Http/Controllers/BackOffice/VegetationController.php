<?php

namespace App\Http\Controllers\BackOffice;

use App\Events\VegetationDataChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\BackOffice\Vegetation\CreateRequest;
use App\Http\Requests\BackOffice\Vegetation\UpdateRequest;
use App\Models\Area;
use App\Models\Species;
use App\Models\Vegetation;
use App\Models\VegetationStatus;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class VegetationController
 * @package App\Http\Controllers
 */
class VegetationController extends Controller
{
  /**
   * @param int $page
   * @param int $itemsPerPage
   * @param array $sortBy
   * @param string|null $search
   * @param bool $withTrashed
   * @param int|null $selectedSpecies
   * @param int|null $selectedStatus
   * @param int|null $selectedArea
   * @return array
   */
  private function listVegetation(
    int     $page,
    int     $itemsPerPage,
    array   $sortBy,
    ?string $search,
    bool    $withTrashed,
    ?int    $selectedSpecies,
    ?int    $selectedStatus,
    ?int    $selectedArea
  ): array
  {
    $queryBuilder = Vegetation::with(
      'status',
      'species',
      'area',
      'comments',
      'mutations',
      'species.type'
    );

    if ($withTrashed) {
      $queryBuilder->withTrashed();
    }

    $queryBuilder->when($selectedSpecies, function ($query, $selectedSpecies) {
      $query->where('specie_id', $selectedSpecies);
    });

    $queryBuilder->when($selectedStatus, function ($query, $selectedStatus) {
      $query->where('status_id', $selectedStatus);
    });

    $queryBuilder->when($selectedArea, function ($query, $selectedArea) {
      $query->where('area_id', $selectedArea);
    });

    if (!empty($sortBy)) {
      // these joins are only needed for sorting
      foreach ($sortBy as $sortByRule) {
        switch ($sortByRule['key']) {
          case 'location':
            $queryBuilder
              ->orderByRaw("CAST(JSON_EXTRACT(`location`, '$.x') AS FLOAT) {$sortByRule['order']}");
            break;
          case 'status.name':
            $queryBuilder
              ->orderBy(
                VegetationStatus::select('name')
                  ->whereColumn('vegetation_status.id', 'vegetations.status_id')
                , $sortByRule['order']
              );
            break;
          case 'species.dutch_name':
            $queryBuilder
              ->orderBy(
                Species::select('dutch_name')
                  ->whereColumn('species.id', 'vegetations.specie_id')
                , $sortByRule['order']
              );
            break;
          case 'species.latin_name':
            $queryBuilder
              ->orderBy(
                Species::select('latin_name')
                  ->whereColumn('species.id', 'vegetations.specie_id')
                , $sortByRule['order']
              );
            break;
          case 'species.blossom_month':
            $queryBuilder
              ->orderBy(
                Species::select('blossom_month')
                  ->whereColumn('species.id', 'vegetations.specie_id')
                , $sortByRule['order']
              );
            break;
          case 'species.height':
            $queryBuilder
              ->orderBy(
                Species::select('height')
                  ->whereColumn('species.id', 'vegetations.specie_id')
                , $sortByRule['order']
              );
            break;
          case 'area.name':
            $queryBuilder
              ->orderBy(
                Area::select('name')
                  ->whereColumn('areas.id', 'vegetations.area_id')
                , $sortByRule['order']
              );
            break;
          default:
            $queryBuilder->orderBy($sortByRule['key'], $sortByRule['order']);
        }
      }
    }

    if (!empty($search)) {
      $queryBuilder->when($search, function ($query, $search) {
        $query->whereAny([
          'placed',
          'number'
        ], 'LIKE', "%$search%");

        $query->orWhereHas('species', function($query) use ($search) {
          $query->whereAny([
            'dutch_name',
            'latin_name'
          ], 'LIKE', "%$search%");
        });

        $query->orWhereHas('group', function($query) use ($search) {
          $query->where('name', 'LIKE', "%$search%");

          $query->orWhereHas('area', function($query) use ($search) {
            $query->where('name', 'LIKE', "%$search%");
          });
        });
      });
    }

    // do a count
    $countBeforePaging = $queryBuilder->count();

    // now set limit
    $queryBuilder->limit($itemsPerPage)->offset(($page - 1) * $itemsPerPage);

    return [
      'items' => $queryBuilder->get()->toArray(),
      'total' => $countBeforePaging
    ];
  }

  /**
   * @return Response
   */
  public function index(): Response
  {
    return inertia('BackOffice/Vegetation/Index', [
      'species' => Species::all(),
      'status' => VegetationStatus::all(),
      'areas' => Area::all()
    ]);
  }

  /**
   * Display the specified resource.
   *
   * @param Request $request
   * @return JsonResponse
   */
  public function list(Request $request): JsonResponse
  {
    $withTrashed = $request->boolean('withTrashed');
    $page = $request->integer('page');
    $itemsPerPage = $request->integer('itemsPerPage');
    $sortBy = $request->post('sortBy');
    $search = $request->post('search');
    $selectedSpecie = $request->post('selectedSpecieValue');
    $selectedStatus = $request->post('selectedStatusValue');
    $selectedArea = $request->post('selectedAreaValue');

    return response()->json(
      $this->listVegetation(
        page: $page,
        itemsPerPage: $itemsPerPage,
        sortBy: $sortBy,
        search: $search,
        withTrashed: $withTrashed,
        selectedSpecies: $selectedSpecie,
        selectedStatus: $selectedStatus,
        selectedArea: $selectedArea
      )
    );
  }

  /**
   * @return Response
   */
  public function create(): Response
  {
    return inertia('BackOffice/Vegetation/Create',[
      'species' => Species::all(),
      'status' => VegetationStatus::all(),
    ]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param CreateRequest $request
   * @return Redirector|Application|RedirectResponse
   */
  public function store(CreateRequest $request): Redirector|Application|RedirectResponse
  {
    $validated = $request->validated();

    Vegetation::create($validated);

    VegetationDataChanged::dispatch();

    return redirect(route('vegetation.index'))
      ->with('success', 'vegetation.messages.created');
  }

  /**
   * @param Vegetation $vegetation
   * @return Response
   */
  public function show(Vegetation $vegetation): Response
  {
    $vegetation->load('species', 'area', 'comments', 'mutations');

    return inertia('BackOffice/Vegetation/Show', [
      'vegetation' => $vegetation,
      'species' => Species::orderBy('dutch_name', 'asc')->get(),
      'status' => VegetationStatus::all(),
    ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param UpdateRequest $request
   * @param Vegetation $vegetation
   * @return Application|RedirectResponse|Redirector
   */
  public function update(UpdateRequest $request, Vegetation $vegetation): Redirector|RedirectResponse|Application
  {
    $validated = $request->validated();

    // update area
    $vegetation->update($validated);

    VegetationDataChanged::dispatch();

    return redirect(route('vegetation.index'))
      ->with('success', 'vegetation.messages.updated');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param Vegetation $vegetation
   * @return Redirector|RedirectResponse|Application
   */
  public function destroy(Vegetation $vegetation): Redirector|RedirectResponse|Application
  {
    $vegetation->delete();

    VegetationDataChanged::dispatch();

    return redirect(route('vegetation.index'))
      ->with('success', 'vegetation.messages.deleted');
  }

  /**
   * Restore the specified resource from storage.
   *
   * @param int $vegetationId
   * @return Redirector|RedirectResponse|Application
   */
  public function restore(int $vegetationId): Redirector|RedirectResponse|Application
  {
    Vegetation::withTrashed()->find($vegetationId)->restore();

    VegetationDataChanged::dispatch();

    return redirect(route('vegetation.index'))
      ->with('success', 'vegetation.messages.restored');
  }

  /**
   * @param Vegetation $vegetation
   * @return BinaryFileResponse
   */
  public function downloadBoard(Vegetation $vegetation): BinaryFileResponse
  {
    return response()->download(
      storage_path("app/boards/{$vegetation->uuid}.svg"),
      $vegetation->uuid . '.svg'
    );
  }
}
