<?php

namespace App\Http\Controllers;

use App\Http\Requests\Public\Comment\CreateRequest;
use App\Models\Comment;
use App\Models\Vegetation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Inertia\Response;

class CommentController extends Controller
{
  /**
   * @param Vegetation $vegetation
   * @return Response
   */
  public function create(Vegetation $vegetation): Response
  {
    return inertia('Public/Comment/Create', [
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
    Comment::create($validated);

    return redirect(route('public.vegetation.show', ['vegetation' => $vegetation->uuid]))
      ->with('success', 'comment.messages.created');
  }
}
