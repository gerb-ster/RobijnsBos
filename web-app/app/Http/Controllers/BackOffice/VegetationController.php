<?php

namespace App\Http\Controllers\BackOffice;

use App\Events\VegetationDataChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\BackOffice\Vegetation\CreateRequest;
use App\Http\Requests\BackOffice\Vegetation\UpdateRequest;
use App\Models\Group;
use App\Models\Species;
use App\Models\Vegetation;
use App\Models\VegetationStatus;
use App\Tools\BoardGenerator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
   * @param int|null $selectedGroup
   * @param int|null $selectedSpecies
   * @param int|null $selectedStatus
   * @return array
   */
  private function listVegetation(
    int     $page,
    int     $itemsPerPage,
    array   $sortBy,
    ?string $search,
    bool    $withTrashed,
    ?int    $selectedGroup,
    ?int    $selectedSpecies,
    ?int    $selectedStatus
  ): array
  {
    $queryBuilder = Vegetation::with('status', 'species', 'group', 'group.area', 'comments', 'mutations');

    if ($withTrashed) {
      $queryBuilder->withTrashed();
    }

    $queryBuilder->when($selectedGroup, function ($query, $selectedGroup) {
      $query->where('group_id', $selectedGroup);
    });

    $queryBuilder->when($selectedSpecies, function ($query, $selectedSpecies) {
      $query->where('specie_id', $selectedSpecies);
    });

    $queryBuilder->when($selectedStatus, function ($query, $selectedStatus) {
      $query->where('status_id', $selectedStatus);
    });

    if (!empty($sortBy)) {
      // these joins are only needed for sorting
      foreach ($sortBy as $sortByRule) {
        $queryBuilder->orderBy($sortByRule['key'], $sortByRule['order']);
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
      'groups' => Group::with('area')->get()
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
    $selectedGroup = $request->post('selectedGroupValue');
    $selectedSpecie = $request->post('selectedSpecieValue');
    $selectedStatus = $request->post('selectedStatusValue');

    return response()->json(
      $this->listVegetation(
        page: $page,
        itemsPerPage: $itemsPerPage,
        sortBy: $sortBy,
        search: $search,
        withTrashed: $withTrashed,
        selectedGroup: $selectedGroup,
        selectedSpecies: $selectedSpecie,
        selectedStatus: $selectedStatus
      )
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
    $vegetation->load('species', 'group', 'comments', 'mutations');

    return inertia('BackOffice/Vegetation/Show', [
      'vegetation' => $vegetation,
      'groups' => Group::with('area')->orderBy('name')->get(),
      'species' => Species::orderBy('dutch_name', 'asc')->get()
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

  /**
   * @param Vegetation $vegetation
   * @return StreamedResponse
   */
  public function showBoard(Vegetation $vegetation): StreamedResponse
  {
    if (!file_exists(storage_path("app/boards/{$vegetation->uuid}.svg"))) {
      $boardGenerator = new BoardGenerator($vegetation);
      $boardGenerator->render();
    }

    $svgContent = file_get_contents(storage_path("app/boards/{$vegetation->uuid}.svg"));

    return response()
      ->stream(function () use ($svgContent) {
        echo $svgContent;
      }, 200, ['Content-Type' => 'image/svg+xml']);
  }
}
