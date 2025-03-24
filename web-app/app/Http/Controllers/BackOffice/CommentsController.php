<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\BackOffice\Vegetation\UpdateRequest;
use App\Models\Comment;
use App\Models\Vegetation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Response;

/**
 * Class CommentsController
 * @package App\Http\Controllers
 */
class CommentsController extends Controller
{
  /**
   * @param Vegetation $vegetation
   * @param int $page
   * @param int $itemsPerPage
   * @param array $sortBy
   * @param string|null $search
   * @param bool $withTrashed
   * @return array
   */
  private function listComments(
    Vegetation $vegetation,
    int     $page,
    int     $itemsPerPage,
    array   $sortBy,
    ?string $search,
    bool    $withTrashed
  ): array
  {
    $queryBuilder = Comment::with('status')->where('vegetation_id', $vegetation->id);

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
   * @param Vegetation $vegetation
   * @param Request $request
   * @return JsonResponse
   */
  public function list(Vegetation $vegetation, Request $request): JsonResponse
  {
    $withTrashed = $request->boolean('withTrashed');
    $page = $request->integer('page');
    $itemsPerPage = $request->integer('itemsPerPage');
    $sortBy = $request->post('sortBy');
    $search = $request->post('search');

    return response()->json(
      $this->listComments($vegetation, $page, $itemsPerPage, $sortBy, $search, $withTrashed)
    );
  }

  /**
   * @param Vegetation $vegetation
   * @param Comment $mutation
   * @return Response
   */
  public function show(Vegetation $vegetation, Comment $mutation): Response
  {
    return inertia('BackOffice/Comment/Show', [
      'vegetation' => $vegetation,
      'mutation' => $mutation
    ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param Vegetation $vegetation
   * @param UpdateRequest $request
   * @param Comment $mutation
   * @return Application|RedirectResponse|Redirector
   */
  public function update(Vegetation $vegetation, UpdateRequest $request, Comment $mutation): Redirector|RedirectResponse|Application
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
   * @param Vegetation $vegetation
   * @param Comment $mutation
   * @return Redirector|RedirectResponse|Application
   */
  public function destroy(Vegetation $vegetation, Comment $mutation): Redirector|RedirectResponse|Application
  {
    $mutation->delete();

    return redirect(route('vegetation.index'))
      ->with('success', 'mutations.messages.deleted');
  }

  /**
   * Restore the specified resource from storage.
   *
   * @param Vegetation $vegetation
   * @param int $mutationId
   * @return Redirector|RedirectResponse|Application
   */
  public function restore(Vegetation $vegetation, int $mutationId): Redirector|RedirectResponse|Application
  {
    Comment::withTrashed()->find($mutationId)->restore();

    return redirect(route('vegetation.index'))
      ->with('success', 'mutations.messages.restored');
  }
}
