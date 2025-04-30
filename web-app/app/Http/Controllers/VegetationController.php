<?php

namespace App\Http\Controllers;

use App\Models\CommentStatus;
use App\Models\Group;
use App\Models\MutationStatus;
use App\Models\Species;
use App\Models\Vegetation;
use App\Models\VegetationStatus;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VegetationController extends Controller
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
    ?int    $selectedGroup,
    ?int    $selectedSpecies
  ): array
  {
    $queryBuilder = Vegetation::with('status', 'species', 'group', 'group.area', 'comments', 'mutations');

    if (!empty($sortBy)) {
      // these joins are only needed for sorting
      foreach ($sortBy as $sortByRule) {
        $queryBuilder->orderBy($sortByRule['key'], $sortByRule['order']);
      }
    }

    $queryBuilder->when($selectedGroup, function ($query, $selectedGroup) {
      $query->where('group_id', $selectedGroup);
    });

    $queryBuilder->when($selectedSpecies, function ($query, $selectedSpecies) {
      $query->where('specie_id', $selectedSpecies);
    });

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
   * @param string $shortCode
   * @return Redirector|RedirectResponse|Application
   */
  public function redirect(string $shortCode): Redirector|RedirectResponse|Application
  {
    $vegetation = Vegetation::where('qr_shortcode', $shortCode)->firstOrFail();
    return redirect(route('public.vegetation.show', $vegetation->uuid));
  }

  /**
   * @param Vegetation $vegetation
   * @return Response
   */
  public function show(Vegetation $vegetation): Response
  {
    $vegetation->load([
      'status',
      'species',
      'group',
      'group.area',
      'mutations' => fn ($query) => $query->where('status_id', MutationStatus::APPROVED),
      'mutations.user',
      'comments' => fn ($query) => $query->where('status_id', CommentStatus::APPROVED),
      'species.type'
    ]);

    return Inertia::render('Public/Vegetation/Show', [
      'vegetation' => $vegetation
    ]);
  }

  /**
   * @param Vegetation $vegetation
   * @return Response
   */
  public function map(Vegetation $vegetation): Response
  {
    $vegetation->load('species');

    return Inertia::render('Public/Vegetation/Map');
  }

  /**
   * @return StreamedResponse
   */
  public function mapImage(): StreamedResponse
  {
    $svgContent = file_get_contents(storage_path("app/map/full_map.svg"));

    return response()->stream(function () use ($svgContent) {
      echo $svgContent;
    });
  }

  /**
   * @param Vegetation $vegetation
   * @return Response
   */
  public function overview(Vegetation $vegetation): Response
  {
    $vegetation->load('species');

    return Inertia::render('Public/Vegetation/Overview', [
      'species' => Species::all(),
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
    $page = $request->integer('page');
    $itemsPerPage = $request->integer('itemsPerPage');
    $sortBy = $request->post('sortBy');
    $search = $request->post('search');
    $selectedGroup = $request->post('selectedGroupValue');
    $selectedSpecie = $request->post('selectedSpecieValue');

    return response()->json(
      $this->listVegetation(
        page: $page,
        itemsPerPage: $itemsPerPage,
        sortBy: $sortBy,
        search: $search,
        selectedGroup: $selectedGroup,
        selectedSpecies: $selectedSpecie
      )
    );
  }
}
