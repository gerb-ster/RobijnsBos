<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\CommentStatus;
use App\Models\MutationStatus;
use App\Models\SpeciesType;
use App\Models\Vegetation;
use App\Tools\BoardGenerator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VegetationController extends Controller
{
  /**
   * @param int $page
   * @param int $itemsPerPage
   * @param array $sortBy
   * @param string|null $search
   * @param int|null $selectedArea
   * @return array
   */
  private function listVegetation(
    int     $page,
    int     $itemsPerPage,
    array   $sortBy,
    ?string $search,
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

    if (!empty($sortBy)) {
      // these joins are only needed for sorting
      foreach ($sortBy as $sortByRule) {
        $queryBuilder->orderBy($sortByRule['key'], $sortByRule['order']);
      }
    }

    $queryBuilder->when($selectedArea, function ($query, $selectedArea) {
      $query->where('area_id', $selectedArea);
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

        $query->orWhereHas('area', function($query) use ($search) {
          $query->where('name', 'LIKE', "%$search%");
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
      'area',
      'mutations' => fn ($query) => $query->where('status_id', MutationStatus::APPROVED),
      'mutations.user',
      'comments' => fn ($query) => $query->where('status_id', CommentStatus::APPROVED),
      'species.type'
    ]);

    return Inertia::render('Public/Vegetation/Show', [
      'vegetation' => $vegetation,
      'canAdministrate' => Gate::allows('administrate')
    ]);
  }

  /**
   * @param Vegetation $vegetation
   * @return Response
   */
  public function map(Vegetation $vegetation): Response
  {
    return Inertia::render('Public/Vegetation/Map', [
      'speciesTypes' => SpeciesType::all()
    ]);
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
    $page = $request->integer('page');
    $itemsPerPage = $request->integer('itemsPerPage');
    $sortBy = $request->post('sortBy');
    $search = $request->post('search');
    $selectedArea = $request->post('selectedAreaValue');

    return response()->json(
      $this->listVegetation(
        page: $page,
        itemsPerPage: $itemsPerPage,
        sortBy: $sortBy,
        search: $search,
        selectedArea: $selectedArea
      )
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
