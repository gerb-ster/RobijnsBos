<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\BackOffice\Vegetation\CreateRequest;
use App\Http\Requests\BackOffice\Vegetation\UpdateRequest;
use App\Models\Group;
use App\Models\Species;
use App\Models\Vegetation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Response;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class VegetationsController extends Controller
{
  /**
   * @param int $page
   * @param int $itemsPerPage
   * @param array $sortBy
   * @param string|null $search
   * @param bool $withTrashed
   * @return array
   */
  private function listVegetation(
    int     $page,
    int     $itemsPerPage,
    array   $sortBy,
    ?string $search,
    bool    $withTrashed
  ): array
  {
    $queryBuilder = Vegetation::with('status', 'species', 'group', 'group.area', 'comments', 'mutations');

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
    return inertia('BackOffice/Vegetation/Index');
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
      $this->listVegetation($page, $itemsPerPage, $sortBy, $search, $withTrashed)
    );
  }

  /**
   * @return Response
   */
  public function create(): Response
  {
    return inertia('BackOffice/Vegetation/Create',[
      'groups' => Group::with('area')->get(),
      'species' => Species::all()
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

    return redirect(route('vegetation.index'))
      ->with('success', 'vegetation.messages.created');
  }

  /**
   * @param Vegetation $vegetation
   * @return Response
   */
  public function show(Vegetation $vegetation): Response
  {
    $vegetation->load('species', 'group', 'comments', 'mutations');

    return inertia('BackOffice/Vegetation/Show', [
      'vegetation' => $vegetation,
      'groups' => Group::with('area')->get(),
      'species' => Species::all()
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

    return redirect(route('vegetation.index'))
      ->with('success', 'vegetation.messages.restored');
  }
}
