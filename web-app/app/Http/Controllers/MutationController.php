<?php

namespace App\Http\Controllers;

use App\Http\Requests\Public\Mutation\CreateRequest;
use App\Models\Mutation;
use App\Models\Vegetation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Inertia\Response;

class MutationController extends Controller
{
  /**
   * @param Vegetation $vegetation
   * @return Response
   */
  public function create(Vegetation $vegetation): Response
  {
    return inertia('Public/Mutation/Create', [
      'vegetation' => $vegetation
    ]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param Vegetation $vegetation
   * @param CreateRequest $request
   * @return Redirector|Application|RedirectResponse
   */
  public function store(Vegetation $vegetation, CreateRequest $request): Redirector|Application|RedirectResponse
  {
    $validated = $request->validated();

    $validated['vegetation_id'] = $vegetation->id;
    Mutation::create($validated);

    return redirect(route('public.vegetation.show', ['vegetation' => $vegetation->uuid]))
      ->with('success', 'mutation.messages.created');
  }
}
