<?php

namespace App\Http\Controllers\BackOffice\Administration;

use App\Http\Controllers\Controller;
use App\Http\Requests\BackOffice\Administration\Area\CreateRequest;
use App\Http\Requests\BackOffice\Administration\Area\UpdateRequest;
use App\Models\Area;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Response;

/**
 * Class AreaController
 * @package App\Http\Controllers
 */
class AreaController extends Controller
{
  /**
   * @param int $page
   * @param int $itemsPerPage
   * @param array $sortBy
   * @param string|null $search
   * @param bool $withTrashed
   * @return array
   */
  private function listAreas(
    int     $page,
    int     $itemsPerPage,
    array   $sortBy,
    ?string $search,
    bool    $withTrashed
  ): array
  {
    $queryBuilder = Area::query();

    if ($withTrashed) {
      $queryBuilder->withTrashed();
    }

    if (!empty($sortBy)) {
      // these joins are only needed for sorting
      foreach ($sortBy as $sortByRule) {
        $queryBuilder->orderBy($sortByRule['key'], $sortByRule['order']);
      }
    }

    if (!empty($search)) {
      $queryBuilder->where('name', 'LIKE', "%$search%");
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
    return inertia('BackOffice/Administration/Area/Index');
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
      $this->listAreas($page, $itemsPerPage, $sortBy, $search, $withTrashed)
    );
  }

  /**
   * @return Response
   */
  public function create(): Response
  {
    return inertia('BackOffice/Administration/Area/Create');
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

    ray($validated);

    Area::create($validated);

    return redirect(route('areas.index'))
      ->with('success', 'areas.messages.created');
  }

  /**
   * @param Area $area
   * @return Response
   */
  public function show(Area $area): Response
  {
    return inertia('BackOffice/Administration/Area/Show', [
      'area' => $area
    ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param UpdateRequest $request
   * @param Area $area
   * @return Application|RedirectResponse|Redirector
   */
  public function update(UpdateRequest $request, Area $area): Redirector|RedirectResponse|Application
  {
    $validated = $request->validated();

    // update area
    $area->update($validated);

    return redirect(route('areas.index'))
      ->with('success', 'areas.messages.updated');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param Area $area
   * @return Redirector|RedirectResponse|Application
   */
  public function destroy(Area $area): Redirector|RedirectResponse|Application
  {
    $area->delete();

    return redirect(route('areas.index'))
      ->with('success', 'areas.messages.deleted');
  }

  /**
   * Restore the specified resource from storage.
   *
   * @param int $areaId
   * @return Redirector|RedirectResponse|Application
   */
  public function restore(int $areaId): Redirector|RedirectResponse|Application
  {
    Area::withTrashed()->find($areaId)->restore();

    return redirect(route('areas.index'))
      ->with('success', 'areas.messages.restored');
  }
}
