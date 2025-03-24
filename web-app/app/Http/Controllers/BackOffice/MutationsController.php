<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\BackOffice\Vegetation\UpdateRequest;
use App\Models\Mutation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Response;

/**
 * Class MutationsController
 * @package App\Http\Controllers
 */
class MutationsController extends Controller
{
  /**
   * @param int $page
   * @param int $itemsPerPage
   * @param array $sortBy
   * @param string|null $search
   * @param bool $withTrashed
   * @return array
   */
  private function listMutations(
    int     $page,
    int     $itemsPerPage,
    array   $sortBy,
    ?string $search,
    bool    $withTrashed
  ): array
  {
    $queryBuilder = Mutation::with('status');

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
      $this->listMutations($page, $itemsPerPage, $sortBy, $search, $withTrashed)
    );
  }

  /**
   * @param Mutation $mutation
   * @return Response
   */
  public function show(Mutation $mutation): Response
  {
    return inertia('BackOffice/Mutation/Show', [
      'mutation' => $mutation
    ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param UpdateRequest $request
   * @param Mutation $mutation
   * @return Application|RedirectResponse|Redirector
   */
  public function update(UpdateRequest $request, Mutation $mutation): Redirector|RedirectResponse|Application
  {
    $validated = $request->validated();

    // update area
    $mutation->update($validated);

    return redirect(route('vegetation.index'))
      ->with('success', 'mutations.messages.updated');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param Mutation $mutation
   * @return Redirector|RedirectResponse|Application
   */
  public function destroy(Mutation $mutation): Redirector|RedirectResponse|Application
  {
    $mutation->delete();

    return redirect(route('vegetation.index'))
      ->with('success', 'mutations.messages.deleted');
  }

  /**
   * Restore the specified resource from storage.
   *
   * @param int $mutationId
   * @return Redirector|RedirectResponse|Application
   */
  public function restore(int $mutationId): Redirector|RedirectResponse|Application
  {
    Mutation::withTrashed()->find($mutationId)->restore();

    return redirect(route('vegetation.index'))
      ->with('success', 'mutations.messages.restored');
  }
}
