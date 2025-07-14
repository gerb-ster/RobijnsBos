<?php

namespace App\Http\Controllers\BackOffice\Administration;

use App\Events\VegetationDataChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\BackOffice\Administration\Species\CreateRequest;
use App\Http\Requests\BackOffice\Administration\Species\UpdateRequest;
use App\Models\LatinFamily;
use App\Models\Role;
use App\Models\Species;
use App\Models\SpeciesType;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Response;

/**
 * Class SpeciesController
 * @package App\Http\Controllers
 */
class SpeciesController extends Controller
{
  /**
   * @param int $page
   * @param int $itemsPerPage
   * @param array $sortBy
   * @param string|null $search
   * @param bool $withTrashed
   * @return array
   */
  private function listSpecies(
    int     $page,
    int     $itemsPerPage,
    array   $sortBy,
    ?string $search,
    bool    $withTrashed
  ): array
  {
    $queryBuilder = Species::withCount('vegetation')->with('latinFamily', 'type');

    if ($withTrashed) {
      $queryBuilder->withTrashed();
    }

    if (!empty($sortBy)) {
      // these joins are only needed for sorting
      foreach ($sortBy as $sortByRule) {
        switch ($sortByRule['key']) {
          case 'type.name':
            $queryBuilder
              ->orderBy(
                SpeciesType::select('name')
                  ->whereColumn('species_types.id', 'species.type_id')
                , $sortByRule['order']
              );
            break;
          case 'latin_family.name':
            $queryBuilder
              ->orderBy(
                LatinFamily::select('name')
                  ->whereColumn('latin_families.id', 'species.latin_family_id')
                , $sortByRule['order']
              );
            break;
          default:
            $queryBuilder->orderBy($sortByRule['key'], $sortByRule['order']);
        }
      }
    }

    if (!empty($search)) {
      $queryBuilder->whereAny([
        'dutch_name',
        'latin_name'
      ], 'LIKE', "%$search%");
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
    return inertia('BackOffice/Administration/Species/Index');
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

    return response()->json(
      $this->listSpecies($page, $itemsPerPage, $sortBy, $search, $withTrashed)
    );
  }

  /**
   * @return Response
   */
  public function create(): Response
  {
    return inertia('BackOffice/Administration/Species/Create', [
      'speciesTypes' => SpeciesType::all()->toArray()
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

    Species::create($validated);

    VegetationDataChanged::dispatch();

    return redirect(route('species.index'))
      ->with('success', 'species.messages.created');
  }

  /**
   * @param Species $species
   * @return Response
   */
  public function show(Species $species): Response
  {
    return inertia('BackOffice/Administration/Species/Show', [
      'species' => $species,
      'types' => SpeciesType::all()
    ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param UpdateRequest $request
   * @param Species $species
   * @return Application|RedirectResponse|Redirector
   */
  public function update(UpdateRequest $request, Species $species): Redirector|RedirectResponse|Application
  {
    $validated = $request->validated();

    // update area
    $species->update($validated);

    VegetationDataChanged::dispatch();

    return redirect(route('species.index'))
      ->with('success', 'species.messages.updated');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param Species $species
   * @return Redirector|RedirectResponse|Application
   */
  public function destroy(Species $species): Redirector|RedirectResponse|Application
  {
    $species->delete();

    return redirect(route('species.index'))
      ->with('success', 'species.messages.deleted');
  }

  /**
   * Restore the specified resource from storage.
   *
   * @param int $speciesId
   * @return Redirector|RedirectResponse|Application
   */
  public function restore(int $speciesId): Redirector|RedirectResponse|Application
  {
    Species::withTrashed()->find($speciesId)->restore();

    return redirect(route('species.index'))
      ->with('success', 'species.messages.restored');
  }
}
